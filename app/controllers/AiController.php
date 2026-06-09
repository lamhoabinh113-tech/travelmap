<?php
/**
 * Travel Memory AI
 * Gợi ý lịch trình, điểm check-in, note địa điểm đẹp và caption.
 * Nguồn mở dùng khi có thể: OpenStreetMap/Nominatim, Overpass, Wikipedia.
 */

require_once '../config/database.php';

class AiController {
    private $db;
    private $knowledgeFile;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }
        $database = new Database();
        $this->db = $database->getConnection();

        $storageDir = dirname(__DIR__, 2) . '/storage';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }
        $this->knowledgeFile = $storageDir . '/ai_knowledge.json';

        $aiConfig = dirname(__DIR__, 2) . '/config/ai.php';
        if (is_readable($aiConfig)) {
            require $aiConfig;
        }
    }

    public function index() {
        require_once '../app/views/location/ai_chat.php';
    }

    public function ask() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $question = isset($_POST['question']) ? trim($_POST['question']) : '';
        $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
        $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

        if ($question === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập câu hỏi.'], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $answer = $this->getAiResponse($question, $latitude, $longitude);
        $this->rememberChatTurn($question, $answer);

        echo json_encode([
            'success' => true,
            'message' => $answer
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function getAiResponse($question, $latitude = null, $longitude = null) {
        $context = $this->collectTravelContext($question, $latitude, $longitude, $_SESSION['user_id']);
        $localAnswer = $this->smartLocalRespond($question, $context);

        $geminiKey = getenv('GEMINI_API_KEY');
        if ($geminiKey) {
            $remoteAnswer = $this->geminiChat($question, $latitude, $longitude, $context, $geminiKey);
            if (!$this->isWeakAiReply($remoteAnswer)) {
                return $remoteAnswer;
            }
        }

        $openaiKey = getenv('OPENAI_API_KEY');
        if ($openaiKey) {
            $remoteAnswer = $this->openAiChat($question, $latitude, $longitude, $context, $openaiKey);
            if (!$this->isWeakAiReply($remoteAnswer)) {
                return $remoteAnswer;
            }
        }

        return $localAnswer;
    }

    private function isWeakAiReply($text) {
        if (!$text || $this->textLength($text) < 40) {
            return true;
        }
        $weak = ['xin lỗi', 'không thể', 'không rõ', 'as an ai', 'tôi là ngôn ngữ'];
        $lower = $this->lower($text);
        foreach ($weak as $w) {
            if (strpos($lower, $w) !== false) {
                return true;
            }
        }
        return false;
    }

    private function rememberChatTurn($question, $answer) {
        if (!isset($_SESSION['ai_chat_history'])) {
            $_SESSION['ai_chat_history'] = [];
        }
        $_SESSION['ai_chat_history'][] = [
            'q' => $this->limitText($question, 200),
            'a' => $this->limitText($answer, 280),
            't' => time()
        ];
        $_SESSION['ai_chat_history'] = array_slice($_SESSION['ai_chat_history'], -5);
    }

    private function getTravelKnowledgeBase() {
        static $base = null;
        if ($base === null) {
            $path = dirname(__DIR__) . '/data/travel_knowledge_vn.php';
            $base = is_readable($path) ? require $path : [];
        }
        return is_array($base) ? $base : [];
    }

    private function getSystemPrompt($context) {
        return 'Bạn là Travel Memory AI — trợ lý du lịch siêu năng động, đam mê xê dịch, luôn tràn đầy năng lượng và am hiểu sâu sắc về du lịch Việt Nam. Bạn được tích hợp trực tiếp vào ứng dụng "Travel Memory Map" (Bản đồ ký ức du lịch).

NHIỆM VỤ CHÍNH:
1. GỢI Ý LỘ TRÌNH DI CHUYỂN & ĐIỂM ĐI - ĐIỂM ĐẾN:
   - Khi người dùng hỏi về cách đi từ điểm A đến điểm B (ví dụ: Hà Nội -> Đà Nẵng, Sài Gòn -> Đà Lạt, v.v.), hãy gợi ý chi tiết: các phương tiện di chuyển (máy bay, tàu hỏa, xe khách, phượt xe máy), thời gian đi, khoảng cách, chi phí ước tính và các cung đường/điểm dừng chân ngắm cảnh dọc đường tuyệt đẹp (ví dụ: đèo Hải Vân, vịnh Lăng Cô, v.v.).
2. LẬP LỊCH TRÌNH CHI TIẾT & ĐA DẠNG:
   - Thiết kế lịch trình du lịch cụ thể theo ngày và buổi (Sáng - Trưa - Chiều - Tối) linh hoạt cho các khoảng thời gian (1 ngày, 2N1Đ, 3N2Đ, 4N3Đ, v.v.).
   - Cá nhân hóa lịch trình theo phong cách xê dịch: phượt bụi phiêu lưu, nghỉ dưỡng chill nhẹ nhàng, du lịch ẩm thực (food tour), du lịch cặp đôi lãng mạn, hoặc đi nhóm bạn năng động.
3. GỢI Ý ĐỊA ĐIỂM & ĐẶC SẢN ĐỊA PHƯƠNG:
   - Gợi ý các danh lam thắng cảnh nổi tiếng, góc chụp ảnh sống ảo độc lạ, các món ăn đặc sản vùng miền nhất định phải thử và các quán ăn được người bản địa đánh giá cao. Ưu tiên sử dụng dữ liệu POI từ ngữ cảnh nếu có.
4. TƯƠNG TÁC VỚI TÍNH NĂNG CỦA WEB "TRAVEL MEMORY MAP":
   - Nhắc nhở và khuyến khích người dùng tận dụng các tính năng của trang web:
     * Tạo một "Chuyến đi" (Trip) mới trên web để quản lý hành trình này.
     * Check-in ghim vị trí lên bản đồ để vẽ đường lộ trình "Marching Ants" (kiến bò) tuyệt đẹp.
     * Chụp ảnh nhanh bằng Locket Widget để lưu lại khoảnh khắc tức thì.
     * Thả cảm xúc (Hạnh phúc, Tuyệt vời, Bình yên...) và viết nhật ký/caption cho mỗi check-in.
     * Mở khóa các huy chương thành tựu (Thám Hiểm, Cú Đêm, Locket Master...) trên web.

CÁCH TRÌNH BÀY & TONE GIỌNG:
- Trả lời bằng tiếng Việt, văn phong hiện đại, cởi mở, truyền cảm hứng xê dịch ("xách balo lên và đi"), nhiều năng lượng, sử dụng các emoji phù hợp (🚗, ✈️, 🏔️, 📸, 🍜).
- Định dạng rõ ràng bằng các tiêu đề, danh sách dạng gạch đầu dòng (bullet points) để người dùng dễ đọc trên cả điện thoại và máy tính.
- Tuyệt đối không trả lời chung chung hoặc bịa đặt địa danh không tồn tại. Nếu không đủ dữ liệu, hãy gợi ý giải pháp tham khảo và hướng dẫn người dùng cung cấp thêm thông tin (ngày đi, ngân sách, điểm xuất phát).';
    }

    private function geminiChat($question, $latitude, $longitude, $context, $apiKey) {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey;
        $systemInstruction = $this->getSystemPrompt($context);
        $userPrompt = $this->buildPrompt($question, $latitude, $longitude, $context);

        $payload = [
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]]
            ],
            'contents' => [
                ['parts' => [['text' => $userPrompt]]]
            ]
        ];

        $response = $this->httpPost($url, json_encode($payload, JSON_UNESCAPED_UNICODE), ['Content-Type: application/json']);
        if (!$response) return null;

        $data = json_decode($response, true);
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return trim($data['candidates'][0]['content']['parts'][0]['text']);
        }
        return null;
    }


    private function openAiChat($question, $latitude, $longitude, $context, $apiKey) {
        $payload = [
            'model' => getenv('OPENAI_MODEL') ?: 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $this->getSystemPrompt($context)],
                ['role' => 'user', 'content' => $this->buildPrompt($question, $latitude, $longitude, $context)]
            ],
            'max_tokens' => 1400,
            'temperature' => 0.82
        ];

        $response = $this->httpPost(
            'https://api.openai.com/v1/chat/completions',
            json_encode($payload, JSON_UNESCAPED_UNICODE),
            [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ]
        );

        if (!$response) {
            return $this->fallbackTravelMemoryResponse($question, $latitude, $longitude, $context);
        }

        $data = json_decode($response, true);
        if (isset($data['choices'][0]['message']['content'])) {
            return trim($data['choices'][0]['message']['content']);
        }

        return $this->fallbackTravelMemoryResponse($question, $latitude, $longitude, $context);
    }

    private function fallbackTravelMemoryResponse($question, $latitude, $longitude, $context) {
        return $this->smartLocalRespond($question, $context);
    }

    private function smartLocalRespond($question, $context) {
        $questionLower = $this->lower($question);
        $scores = $this->scoreIntents($questionLower);

        // Check for start & end route points first
        if (($scores['route'] ?? 0) >= 2) {
            $routePoints = $this->detectRoutePoints($question);
            if ($routePoints['start'] && $routePoints['end']) {
                return $this->composeRouteAnswer($routePoints['start'], $routePoints['end'], $context);
            }
        }

        if (($scores['caption'] ?? 0) >= 3) {
            return $this->composeCaption($context);
        }
        if (($scores['journal'] ?? 0) >= 3) {
            return $this->composeJournal($context);
        }
        if (($scores['itinerary'] ?? 0) >= 2 && ($scores['itinerary'] ?? 0) >= ($scores['food'] ?? 0)) {
            return $this->composeItinerary($question, $context);
        }
        if (($scores['weather'] ?? 0) >= 2 && ($scores['weather'] ?? 0) >= ($scores['scenic'] ?? 0)) {
            return $this->composeWeatherAnswer($context);
        }
        if (($scores['food'] ?? 0) >= 2) {
            return $this->composeFoodAnswer($question, $context);
        }
        if (($scores['scenic'] ?? 0) >= 2 || ($scores['checkin'] ?? 0) >= 2) {
            return $this->composeScenicAnswer($context);
        }

        return $this->composeAdvisorAnswer($question, $context, $scores);
    }

    private function scoreIntents($questionLower) {
        $scores = [
            'weather' => 0, 'itinerary' => 0, 'scenic' => 0, 'checkin' => 0,
            'food' => 0, 'caption' => 0, 'journal' => 0, 'route' => 0, 'advisor' => 1
        ];

        $map = [
            'weather' => ['thời tiết', 'thoi tiet', 'mưa', 'mua', 'nắng', 'nong', 'nóng', 'lạnh', 'dự báo', 'du bao', 'forecast'],
            'itinerary' => ['lịch trình', 'lich trinh', 'tour', 'kế hoạch', 'ke hoach', '3n2d', '4n3d', 'ngày', 'đêm', 'plan', 'hành trình'],
            'scenic' => ['đẹp', 'đi đâu', 'di dau', 'tham quan', 'địa điểm', 'dia diem', 'nên đi', 'nen di', 'view', 'cảnh', 'landmark'],
            'checkin' => ['check-in', 'checkin', 'chụp ảnh', 'chup anh', 'sống ảo', 'góc chụp'],
            'food' => ['ăn gì', 'an gi', 'đồ ăn', 'do an', 'món ngon', 'mon ngon', 'quán', 'quan ', 'nhà hàng', 'đặc sản', 'cafe', 'cà phê', 'food'],
            'caption' => ['caption', 'hashtag', 'chú thích'],
            'journal' => ['nhật ký', 'nhat ky', 'story', 'câu chuyện'],
            'route' => ['đi từ', 'di tu', 'đến từ', 'den tu', 'lộ trình', 'lo trinh', 'đường đi', 'duong di', 'phương tiện', 'phuong tien', 'di chuyển', 'di chuyen', 'khoảng cách', 'khoang cach', 'mất bao lâu', 'mat bao lau', 'xe khách', 'xe khach', 'máy bay', 'may bay', 'tàu hỏa', 'tau hoa', 'xe máy', 'xe may', 'phượt', 'phuot', 'di tu', 'tu ', 'từ ']
        ];

        foreach ($map as $intent => $words) {
            foreach ($words as $w) {
                if (strpos($questionLower, $w) !== false) {
                    $scores[$intent] += (strpos($w, ' ') !== false) ? 2 : 1;
                }
            }
        }

        if (preg_match('/\d+\s*ngày/iu', $questionLower)) {
            $scores['itinerary'] += 3;
        }

        return $scores;
    }

    private function composeAdvisorAnswer($question, $context, $scores) {
        $place = $this->contextPlaceName($context);
        $curated = $context['curated'] ?? null;
        $parts = [];

        $parts[] = $this->buildPersonalizedOpener($context, $question);

        if ($curated && !empty($curated['intro'])) {
            $parts[] = "📌 Về {$curated['name']}\n" . $curated['intro'];
            if (!empty($curated['vibe'])) {
                $parts[] = "Vibe hợp: " . $curated['vibe'] . ".";
            }
        } elseif (!empty($context['summary'])) {
            $parts[] = "📌 Tổng quan\n" . $context['summary'];
        }

        if (!empty($context['weather']['days'])) {
            $parts[] = $this->generateWeatherForecast($context);
        }

        $spots = $this->mergedSpots($context, 6);
        $foods = $this->mergedFoods($context, 5);

        if (!empty($spots)) {
            $parts[] = "🏞️ Địa điểm đáng ghé\n" . $this->formatBulletList($spots, 'visit');
        }
        if (!empty($foods)) {
            $parts[] = "🍽️ Ăn gì / quán nên thử\n" . $this->formatBulletList($foods, 'food');
        }

        if ($curated && !empty($curated['photo'])) {
            $parts[] = "📸 Góc chụp & thời điểm: " . $curated['photo'];
        }

        $parts[] = $this->buildActionableClosing($context, $scores);

        return trim(implode("\n\n", array_filter($parts)));
    }

    private function composeItinerary($question, $context) {
        return $this->generateItinerarySuggestion($question, $context);
    }

    private function composeWeatherAnswer($context) {
        $intro = $this->buildPersonalizedOpener($context, 'thời tiết');
        return trim($intro . "\n\n" . $this->generateWeatherForecast($context));
    }

    private function composeFoodAnswer($question, $context) {
        $intro = $this->buildPersonalizedOpener($context, $question);
        return trim($intro . "\n\n" . $this->generateFoodAdvisorResponse($question, $context));
    }

    private function composeScenicAnswer($context) {
        $place = $this->contextPlaceName($context);
        $spots = $this->mergedSpots($context, 8);
        $parts = [$this->buildPersonalizedOpener($context, 'địa điểm')];

        if (!empty($spots)) {
            $parts[] = "🏞️ Gợi ý địa điểm đẹp" . ($place ? " tại {$place}" : "") . "\n"
                . $this->formatBulletList($spots, 'visit');
        } else {
            $parts[] = $this->generateBeautifulPlaceNotes($context);
        }

        $curated = $context['curated'] ?? null;
        if ($curated && !empty($curated['photo'])) {
            $parts[] = "📸 " . $curated['photo'];
        }
        $parts[] = $this->generateCheckinSuggestion($context);

        return trim(implode("\n\n", $parts));
    }

    private function composeCaption($context) {
        $place = $this->contextPlaceName($context);
        $mood = $this->pickMoodFromJourneys($context);
        $lines = [
            $this->buildPersonalizedOpener($context, 'caption'),
            "✍️ Vài caption bạn có thể dùng:",
            "1. " . ($place ? "{$place} — " : "") . "một ngày {$mood}, đủ để nhớ lâu.",
            "2. Không cần đi xa — chỉ cần đúng người, đúng ánh sáng, và vài tấm ảnh thật lòng.",
            "3. Gắn ghim lên bản đồ ký ức, để sau này mở lại vẫn thấy mình đã từng rất vui.",
            "#TravelMemory #" . ($place ? str_replace(' ', '', ucwords($place)) : 'HanhTrinh') . " #Checkin"
        ];
        return implode("\n", $lines);
    }

    private function composeJournal($context) {
        $place = $this->contextPlaceName($context) ?: 'nơi này';
        $weather = $context['weather']['days'][0] ?? null;
        $wLine = $weather ? "Trời {$weather['label']}, {$weather['temp_min']}–{$weather['temp_max']}°C." : '';

        return $this->buildPersonalizedOpener($context, 'nhật ký') . "\n\n"
            . "📔 Nhật ký mẫu:\n"
            . "Sáng nay mình rời đi tới {$place}. {$wLine}\n"
            . "Có những khoảnh khắc nhỏ — tiếng cười, mùi đồ ăn, góc phố quen — khiến chuyến đi trở nên đáng nhớ hơn checklist.\n"
            . "Mình chọn lưu lại vài tấm ảnh và một dòng cảm xúc trên Travel Memory Map, để sau này đọc lại vẫn như đang đứng ngay tại đó.";
    }

    private function buildPersonalizedOpener($context, $topic) {
        $place = $this->contextPlaceName($context);
        $name = $_SESSION['full_name'] ?? 'bạn';
        $line = "Chào {$name}! ";

        if ($place) {
            $line .= "Mình đã nhận diện {$place} trong câu hỏi";
        } else {
            $line .= "Mình đã đọc câu hỏi của bạn";
        }

        if (!empty($context['user_journeys'][0]['place_name'])) {
            $last = $context['user_journeys'][0]['place_name'];
            $line .= " — và thấy gần đây bạn có kỷ niệm tại {$last}";
        }

        $line .= ", nên tư vấn theo hướng thực tế, dễ áp dụng.";
        return $line;
    }

    private function buildActionableClosing($context, $scores) {
        $place = $this->contextPlaceName($context) ?: 'điểm đến này';
        $days = max(1, intval($context['days'] ?: 1));

        $tips = ["💡 Gợi ý tiếp theo:"];
        if (($scores['itinerary'] ?? 0) < 2) {
            $tips[] = "- Hỏi: \"Lịch trình {$days} ngày {$place} cho nhóm bạn, ưu tiên ăn uống\".";
        }
        if (($scores['weather'] ?? 0) < 2 && !empty($context['weather'])) {
            $tips[] = "- Hỏi: \"Thời tiết {$place} tuần này có nên đi không?\".";
        }
        $tips[] = "- Lưu mỗi điểm ghé lên bản đồ để AI hiểu gu du lịch của bạn hơn lần sau.";

        return implode("\n", $tips);
    }

    private function mergedSpots($context, $limit) {
        $items = $this->pickPois($context, ['attractions', 'culture', 'parks'], $limit);
        $curated = $context['curated']['spots'] ?? [];
        return array_slice(array_values(array_unique(array_merge($curated, $items))), 0, $limit);
    }

    private function mergedFoods($context, $limit) {
        $items = $this->pickPois($context, ['food', 'cafes'], $limit);
        $curated = $context['curated']['food'] ?? [];
        return array_slice(array_values(array_unique(array_merge($curated, $items))), 0, $limit);
    }

    private function formatBulletList($items, $mode) {
        $out = '';
        foreach ($items as $i => $name) {
            if ($mode === 'food') {
                $out .= ($i + 1) . ". {$name} — nên chụp món + note giá/mùi vị.\n";
            } else {
                $out .= ($i + 1) . ". {$name} — dành 1–2 tiếng, chụp 3 kiểu ảnh (toàn cảnh / người / chi tiết).\n";
            }
        }
        return trim($out);
    }

    private function pickMoodFromJourneys($context) {
        if (!empty($context['user_journeys'][0]['feeling'])) {
            return $this->lower($context['user_journeys'][0]['feeling']);
        }
        return 'bình yên';
    }

    private function detectIntents($questionLower) {
        $intents = [];

        if ($this->containsAny($questionLower, ['thời tiết', 'thoi tiet', 'mưa', 'mua', 'nắng', 'nong', 'nóng', 'lạnh', 'lanh', 'dự báo', 'du bao', 'forecast', 'weather'])) {
            $intents[] = 'weather';
        }
        if ($this->containsAny($questionLower, ['lịch trình', 'lich trinh', 'itinerary', 'kế hoạch', 'ke hoach', 'tour', 'ngày', 'ngay', 'đêm', 'dem', '3n2d', '4n3d'])) {
            $intents[] = 'itinerary';
        }
        if ($this->containsAny($questionLower, ['đẹp', 'dep', 'view', 'cảnh', 'canh', 'địa điểm', 'dia diem', 'đi đâu', 'di dau', 'tham quan', 'note', 'nên ghé', 'nen ghe', 'landmark'])) {
            $intents[] = 'scenic';
        }
        if ($this->containsAny($questionLower, ['check-in', 'checkin', 'sống ảo', 'song ao', 'chụp ảnh', 'chup anh', 'góc chụp', 'goc chup'])) {
            $intents[] = 'checkin';
        }
        if ($this->containsAny($questionLower, ['ăn', 'an ', 'đồ ăn', 'do an', 'món', 'mon ', 'ngon', 'quán', 'quan ', 'nhà hàng', 'nha hang', 'cafe', 'cà phê', 'ca phe', 'đặc sản', 'dac san', 'food'])) {
            $intents[] = 'food';
        }
        if ($this->containsAny($questionLower, ['caption', 'chú thích', 'chu thich', 'hashtag'])) {
            $intents[] = 'caption';
        }
        if ($this->containsAny($questionLower, ['nhật ký', 'nhat ky', 'story', 'câu chuyện', 'cau chuyen'])) {
            $intents[] = 'journal';
        }

        return array_values(array_unique($intents));
    }

    private function collectTravelContext($question, $latitude, $longitude, $userId = null) {
        $destination = $this->extractDestination($question);
        $days = $this->extractDays($question);

        $context = [
            'destination' => $destination,
            'days' => $days,
            'lat' => $latitude,
            'lon' => $longitude,
            'display_name' => null,
            'summary' => null,
            'weather' => null,
            'curated' => null,
            'user_journeys' => [],
            'cached_knowledge' => null,
            'chat_history' => $_SESSION['ai_chat_history'] ?? [],
            'sources' => [],
            'pois' => [
                'attractions' => [],
                'food' => [],
                'cafes' => [],
                'parks' => [],
                'culture' => []
            ]
        ];

        if (!$destination && $latitude && $longitude) {
            $reverse = $this->reverseGeocode($latitude, $longitude);
            if ($reverse) {
                $destination = $reverse;
                $context['destination'] = $destination;
                $context['display_name'] = $reverse;
                $context['sources'][] = 'GPS reverse';
            }
        }

        $context['curated'] = $this->resolveCuratedPack($destination);
        if ($context['curated']) {
            if (!$context['lat'] && !empty($context['curated']['lat'])) {
                $context['lat'] = $context['curated']['lat'];
            }
            if (!$context['lon'] && !empty($context['curated']['lon'])) {
                $context['lon'] = $context['curated']['lon'];
            }
            $context['sources'][] = 'Travel Memory Knowledge';
            $this->injectCuratedPois($context);
            if (empty($context['summary']) && !empty($context['curated']['intro'])) {
                $context['summary'] = $context['curated']['intro'];
            }
        }

        if ($destination) {
            $context['cached_knowledge'] = $this->loadCachedKnowledge($destination);

            $place = $this->fetchNominatimPlace($destination);
            if ($place) {
                $context['lat'] = isset($place['lat']) ? floatval($place['lat']) : $context['lat'];
                $context['lon'] = isset($place['lon']) ? floatval($place['lon']) : $context['lon'];
                $context['display_name'] = $place['display_name'] ?? $destination;
                $context['sources'][] = 'OpenStreetMap/Nominatim';
            }

            $summary = $this->fetchWikipediaSummary($destination);
            if ($summary) {
                $context['summary'] = $summary;
                $context['sources'][] = 'Wikipedia';
            }
        }

        if ($context['lat'] && $context['lon']) {
            $pois = $this->fetchOverpassPois($context['lat'], $context['lon']);
            if (!empty($pois)) {
                $context['pois'] = $this->mergePoiArrays($context['pois'], $pois);
                $context['sources'][] = 'OpenStreetMap/Overpass';
            }

            $context['weather'] = $this->fetchWeatherForecast($context['lat'], $context['lon'], max(3, $days));
            if ($context['weather']) {
                $context['sources'][] = 'Open-Meteo';
            }
        }

        if ($userId) {
            $context['user_journeys'] = $this->fetchUserJourneyContext($userId);
            if (!empty($context['user_journeys'])) {
                $context['sources'][] = 'Hành trình của bạn';
            }
        }

        $this->cacheUsefulKnowledge($destination, $context);
        $context['sources'] = array_values(array_unique($context['sources']));
        return $context;
    }

    private function generateItinerarySuggestion($question, $context) {
        $days = max(1, min(7, intval($context['days'] ?: 1)));
        $placeName = $this->contextPlaceName($context);
        $attractions = $this->mergedSpots($context, 14);
        $food = $this->mergedFoods($context, 10);
        $vibe = $context['curated']['vibe'] ?? 'khám phá, ăn uống, chụp ảnh';

        $out = $this->buildPersonalizedOpener($context, $question) . "\n\n";
        $out .= "🗓️ Lịch trình gợi ý {$days} ngày" . ($placeName ? " — {$placeName}" : "") . "\n";
        $out .= "Vibe: {$vibe}.\n";

        if (!empty($context['summary'])) {
            $out .= "\n" . $context['summary'] . "\n";
        }

        $templates = [
            ['sáng' => 'tham quan điểm nổi bật', 'trưa' => 'ăn đặc sản địa phương', 'chiều' => 'check-in / cafe view', 'tối' => 'dạo phố, chợ đêm hoặc hồ'],
            ['sáng' => 'săn mây / ánh sáng sớm', 'trưa' => 'nghỉ trưa', 'chiều' => 'điểm văn hóa hoặc công viên', 'tối' => 'quán ăn đông khách'],
            ['sáng' => 'biển / công viên ven nước', 'trưa' => 'hải sản hoặc quán cơm', 'chiều' => 'điểm view cao', 'tối' => 'bar/cafe nhạc nhẹ'],
        ];

        for ($day = 1; $day <= $days; $day++) {
            $tpl = $templates[($day - 1) % count($templates)];
            $morning = $this->poiAt($attractions, ($day - 1) * 2) ?: 'khu trung tâm / điểm biểu tượng';
            $afternoon = $this->poiAt($attractions, ($day - 1) * 2 + 1) ?: 'điểm check-in view đẹp';
            $evening = $this->poiAt($food, $day - 1) ?: 'quán địa phương được review tốt';
            $meal = $this->poiAt($food, $day) ?: 'món đặc sản nên thử';

            $out .= "\n📍 Ngày {$day}\n";
            $out .= "• Sáng (7h–11h): {$morning} — {$tpl['sáng']}. Lưu pin trên map + 3 ảnh.\n";
            $out .= "• Trưa (11h–14h): {$meal} tại {$evening}.\n";
            $out .= "• Chiều (14h–18h): {$afternoon} — {$tpl['chiều']}.\n";
            $out .= "• Tối (18h–22h): {$tpl['tối']}; ghi mood + chi phí ước tính.\n";

            if (!empty($context['weather']['days'][$day - 1])) {
                $w = $context['weather']['days'][$day - 1];
                $out .= "  ↳ Thời tiết: {$w['label']}, {$w['temp_min']}–{$w['temp_max']}°C";
                if ($w['rain_chance'] >= 45) {
                    $out .= " — có mưa, chuyển plan trong nhà buổi chiều.";
                }
                $out .= "\n";
            }
        }

        $out .= "Checklist lưu kỷ niệm:\n";
        $out .= "- 1 ảnh toàn cảnh, 2 ảnh người, 1 ảnh món ăn, 1 ảnh khoảnh khắc tự nhiên.\n";
        $out .= "- Gắn tag: #lichtrinh #checkin #foodnote #travelmemory.\n";
        if (!empty($context['weather']['days'][0])) {
            $w = $context['weather']['days'][0];
            $out .= "\nThời tiết ngày đầu: {$w['label']}, {$w['temp_min']}–{$w['temp_max']}°C";
            if (($w['rain_chance'] ?? 0) >= 40) {
                $out .= " — có mưa, nên có plan B trong nhà.";
            }
            $out .= "\n";
        }

        return trim($out);
    }

    private function generateBeautifulPlaceNotes($context) {
        $placeName = $this->contextPlaceName($context);
        $attractions = $this->pickPois($context, ['attractions', 'culture', 'parks'], 10);

        $out = "Note địa điểm đẹp" . ($placeName ? " ở {$placeName}" : "") . ":\n";
        if (!empty($attractions)) {
            foreach ($attractions as $index => $name) {
                $out .= ($index + 1) . ". {$name}\n";
                $out .= "   - Nên lưu: góc chụp, giờ đẹp, mood, lý do muốn quay lại.\n";
            }
            $out .= "\nNguồn tham khảo: " . implode(', ', $context['sources']) . ".";
            return $out;
        }

        return $out
            . "- Điểm ngắm cảnh: hồ, sông, cầu, công viên hoặc nơi có view rộng.\n"
            . "- Điểm văn hóa: đình, chùa, bảo tàng, phố cũ, khu di tích.\n"
            . "- Điểm đời thường: quán cafe nhỏ, chợ địa phương, con đường nhiều cây.\n"
            . "- Mỗi note nên có: tên địa điểm, tọa độ, giờ đẹp, mood, ảnh đại diện.";
    }

    private function generateCheckinSuggestion($context) {
        $placeName = $this->contextPlaceName($context);
        $spots = $this->pickPois($context, ['attractions', 'parks', 'culture'], 8);

        $out = "Gợi ý điểm check-in" . ($placeName ? " tại {$placeName}" : "") . ":\n";
        if (!empty($spots)) {
            foreach ($spots as $spot) {
                $out .= "- {$spot}: chụp 1 ảnh toàn cảnh, 1 ảnh chân dung, 1 ảnh chi tiết nhỏ.\n";
            }
            $out .= "\nKhung giờ đẹp: 6:00-8:00 hoặc 16:30-18:00 để ảnh mềm hơn.";
            return $out;
        }

        return $out
            . "- Chọn nơi có ánh sáng tự nhiên: bờ hồ, công viên, phố đi bộ, rooftop, quán cafe cửa kính.\n"
            . "- Tránh giờ nắng gắt; ưu tiên sáng sớm hoặc hoàng hôn.\n"
            . "- Lưu thêm note: ai đi cùng, mood, bài nhạc hợp với kỷ niệm.";
    }

    private function generateFoodAdvisorResponse($question, $context) {
        $placeName = $this->contextPlaceName($context);
        $food = $this->mergedFoods($context, 10);
        $cafes = $this->pickPois($context, ['cafes'], 6);
        $questionLower = $this->lower($question);

        $out = "🍜 Gợi ý ăn uống" . ($placeName ? " tại {$placeName}" : "") . ":\n";

        if ($this->containsAny($questionLower, ['ngon', 'đặc sản', 'dac san', 'nên ăn', 'nen an', 'món gì'])) {
            $out .= "Phán đoán nhanh: ưu tiên món địa phương, quán đông dân địa phương, và nơi có menu giá rõ.\n\n";
        }

        if (!empty($food)) {
            $out .= "Quán/món nên thử (từ bản đồ nguồn mở):\n";
            foreach ($food as $i => $name) {
                $out .= ($i + 1) . ". {$name} — thử món signature, chụp ảnh món + hóa đơn để lưu album.\n";
            }
        }
        if (!empty($cafes)) {
            $out .= "\nCafe/view đẹp:\n";
            foreach ($cafes as $name) {
                $out .= "- {$name}: hợp nghỉ chân, làm việc nhẹ hoặc chụp ảnh buổi chiều.\n";
            }
        }

        if (empty($food) && empty($cafes)) {
            $out .= "- Buổi sáng: phở/bánh mì/quán nước địa phương.\n"
                . "- Buổi trưa: quán cơm hoặc đặc sản vùng miền.\n"
                . "- Buổi tối: chợ đêm, quán nướng/seafood nếu gần biển.\n"
                . "- Tip: hỏi nhân viên \"món nào bán chạy nhất\" thay vì gọi ngẫu nhiên.\n";
        }

        $out .= "\n💡 Lưu vào Travel Memory: tên quán, món, giá, mood, ảnh món — sau này xem lại rất có giá trị.";
        if (!empty($context['sources'])) {
            $out .= "\nNguồn: " . implode(', ', $context['sources']) . ".";
        }
        return $out;
    }

    private function generateWeatherForecast($context) {
        $placeName = $this->contextPlaceName($context) ?: 'khu vực này';
        $weather = $context['weather'] ?? null;

        if (!$weather || empty($weather['days'])) {
            return "🌤️ Dự báo thời tiết ({$placeName}):\n"
                . "Chưa lấy được dữ liệu thời tiết realtime. Bạn hỏi kèm tên thành phố cụ thể hoặc bật GPS để AI dự báo chính xác hơn.\n"
                . "- Mùa mưa: mang áo mưa, ưu tiên hoạt động trong nhà buổi chiều.\n"
                . "- Nắng: kem chống nắng, nước, tránh 11h-15h.";
        }

        $out = "🌤️ Dự báo thời tiết {$placeName} (nguồn Open-Meteo):\n";
        foreach ($weather['days'] as $day) {
            $out .= "- {$day['date']}: {$day['label']}, {$day['temp_min']}°C → {$day['temp_max']}°C";
            if ($day['rain_chance'] > 0) {
                $out .= ", khả năng mưa ~{$day['rain_chance']}%";
            }
            $out .= "\n";
        }

        $out .= "\nGợi ý linh hoạt:\n";
        $first = $weather['days'][0];
        if (($first['rain_chance'] ?? 0) >= 50) {
            $out .= "- Ngày mai có mưa: ưu tiên bảo tàng, cafe, chợ trong nhà; mang giày chống nước.\n";
        } else {
            $out .= "- Thời tiết ổn: sắp lịch check-in ngoài trời sáng sớm hoặc hoàng hôn.\n";
        }
        return $out;
    }

    private function buildAdvisorIntro($context, $intents) {
        $place = $this->contextPlaceName($context);
        $intro = "🧭 Travel Memory AI";
        if ($place) {
            $intro .= " · {$place}";
        }
        $intro .= "\n";

        if (!empty($context['user_journeys'])) {
            $recent = array_slice($context['user_journeys'], 0, 3);
            $names = array_map(function ($j) { return $j['place_name']; }, $recent);
            $intro .= "Dựa trên hành trình gần đây của bạn (" . implode(', ', $names) . "), mình gợi ý như sau:\n";
        }

        if (!empty($context['cached_knowledge']['tips'])) {
            $intro .= "Kiến thức đã tích lũy: " . $context['cached_knowledge']['tips'][0] . "\n";
        }

        return $intro;
    }

    private function generateFlexibleAdvisorResponse($question, $context) {
        $placeName = $this->contextPlaceName($context);
        $questionLower = $this->lower($question);
        $parts = [];

        $parts[] = "Mình hiểu bạn đang hỏi: \"{$question}\".\n";

        if ($placeName) {
            $parts[] = "📍 Địa điểm liên quan: {$placeName}";
            if (!empty($context['summary'])) {
                $parts[] = "Tóm tắt: " . $context['summary'];
            }
        }

        if (!empty($context['weather'])) {
            $parts[] = $this->generateWeatherForecast($context);
        }

        $attractions = $this->pickPois($context, ['attractions', 'culture', 'parks'], 5);
        $food = $this->pickPois($context, ['food', 'cafes'], 5);

        if ($this->containsAny($questionLower, ['đẹp', 'dep', 'đi đâu', 'di dau', 'tham quan']) && !empty($attractions)) {
            $parts[] = "🏞️ Địa điểm đáng ghé:\n- " . implode("\n- ", $attractions);
        }
        if ($this->containsAny($questionLower, ['ngon', 'ăn', 'an ', 'món', 'food']) && !empty($food)) {
            $parts[] = "🍽️ Gợi ý ăn uống:\n- " . implode("\n- ", $food);
        }

        if ($this->containsAny($questionLower, ['nên', 'có nên', 'co nen', 'đáng', 'dang '])) {
            $parts[] = "⚖️ Phán đoán:\n"
                . "- Nên đi nếu bạn thích trải nghiệm mới và có 1-2 ngày trống.\n"
                . "- Cân nhắc thời tiết + chi phí; chia nhỏ lịch trình sẽ thoải mái hơn tour dồn một ngày.\n";
        }

        if (empty($attractions) && empty($food) && empty($context['weather'])) {
            $parts[] = "Để mình tư vấn sát hơn, bạn cho thêm:\n"
                . "1) Đi đâu (thành phố/khu vực)\n"
                . "2) Mấy ngày, đi một mình hay nhóm\n"
                . "3) Thích biển, núi, ẩm thực hay chụp ảnh?\n\n"
                . "Ví dụ: \"Lịch trình 3 ngày Đà Nẵng, thích biển và đồ ăn ngon\" hoặc \"Thời tiết Hải Dương tuần này\".";
        } else {
            $parts[] = "💬 Bạn có thể hỏi tiếp: lịch trình chi tiết, món ngon cụ thể, hoặc điểm check-in theo vibe chill/couple/friends.";
        }

        return trim(implode("\n\n", $parts));
    }

    private function fetchWeatherForecast($lat, $lon, $days = 5) {
        $days = max(1, min(7, intval($days)));
        $url = 'https://api.open-meteo.com/v1/forecast?latitude=' . rawurlencode($lat)
            . '&longitude=' . rawurlencode($lon)
            . '&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max'
            . '&timezone=Asia%2FBangkok&forecast_days=' . $days;

        $response = $this->httpGet($url, 8);
        if (!$response) return null;

        $data = json_decode($response, true);
        if (empty($data['daily']['time'])) return null;

        $result = ['days' => []];
        foreach ($data['daily']['time'] as $i => $date) {
            $code = intval($data['daily']['weather_code'][$i] ?? 0);
            $result['days'][] = [
                'date' => $date,
                'label' => $this->weatherCodeLabel($code),
                'temp_max' => round(floatval($data['daily']['temperature_2m_max'][$i] ?? 0)),
                'temp_min' => round(floatval($data['daily']['temperature_2m_min'][$i] ?? 0)),
                'rain_chance' => intval($data['daily']['precipitation_probability_max'][$i] ?? 0)
            ];
        }
        return $result;
    }

    private function weatherCodeLabel($code) {
        $map = [
            0 => 'Trời quang', 1 => 'Ít mây', 2 => 'Có mây', 3 => 'Nhiều mây',
            45 => 'Sương mù', 48 => 'Sương đóng băng',
            51 => 'Mưa phùn nhẹ', 53 => 'Mưa phùn', 55 => 'Mưa phùn dày',
            61 => 'Mưa nhẹ', 63 => 'Mưa vừa', 65 => 'Mưa to',
            71 => 'Tuyết nhẹ', 73 => 'Tuyết vừa', 75 => 'Tuyết dày',
            80 => 'Mưa rào nhẹ', 81 => 'Mưa rào', 82 => 'Mưa rào mạnh',
            95 => 'Dông', 96 => 'Dông kèm mưa đá', 99 => 'Dông mạnh'
        ];
        return $map[$code] ?? 'Thời tiết thay đổi';
    }

    private function fetchUserJourneyContext($userId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT place_name, feeling, visit_date FROM locations WHERE user_id = :uid ORDER BY visit_date DESC LIMIT 15"
            );
            $stmt->execute([':uid' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function knowledgeKey($destination) {
        return $this->lower(trim($destination));
    }

    private function loadCachedKnowledge($destination) {
        if (!$destination || !is_readable($this->knowledgeFile)) return null;
        $all = json_decode(file_get_contents($this->knowledgeFile), true);
        if (!is_array($all)) return null;
        return $all[$this->knowledgeKey($destination)] ?? null;
    }

    private function cacheUsefulKnowledge($destination, $context) {
        if (!$destination) return;

        $hasPoi = !empty($this->pickPois($context, ['attractions', 'food', 'cafes', 'culture', 'parks'], 1));
        if (!$hasPoi && empty($context['summary'])) return;

        $all = [];
        if (is_readable($this->knowledgeFile)) {
            $decoded = json_decode(file_get_contents($this->knowledgeFile), true);
            if (is_array($decoded)) $all = $decoded;
        }

        $key = $this->knowledgeKey($destination);
        $tips = [];
        if (!empty($context['summary'])) {
            $tips[] = $this->limitText($context['summary'], 120);
        }
        if ($hasPoi) {
            $tips[] = 'Đã ghi nhận ' . count($this->pickPois($context, ['attractions', 'food', 'cafes', 'culture', 'parks'], 20))
                . ' điểm tham quan/ăn uống từ nguồn mở.';
        }

        $all[$key] = [
            'destination' => $destination,
            'tips' => array_values(array_unique($tips)),
            'updated_at' => date('c')
        ];

        @file_put_contents($this->knowledgeFile, json_encode($all, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    private function generateServiceSuggestion($context) {
        $placeName = $this->contextPlaceName($context);
        $food = $this->pickPois($context, ['food'], 8);
        $cafes = $this->pickPois($context, ['cafes'], 8);

        $out = "Gợi ý ăn uống và nghỉ chân" . ($placeName ? " ở {$placeName}" : "") . ":\n";
        if (!empty($food)) {
            $out .= "Quán ăn nên xem:\n";
            foreach ($food as $name) {
                $out .= "- {$name}\n";
            }
        }
        if (!empty($cafes)) {
            $out .= "Cafe/điểm nghỉ:\n";
            foreach ($cafes as $name) {
                $out .= "- {$name}\n";
            }
        }
        if (!empty($food) || !empty($cafes)) {
            $out .= "\nNote nhanh: lưu món đã thử, giá, ảnh món ăn và đánh dấu có nên quay lại không.";
            return $out;
        }

        return $out
            . "- Ưu tiên quán đông khách địa phương, menu rõ giá, gần điểm tham quan.\n"
            . "- Cafe nên chọn nơi có ánh sáng đẹp, chỗ ngồi thoải mái, view mở.\n"
            . "- Lưu lại món nên thử, khoảng giá và ảnh hóa đơn nếu muốn thống kê chi phí.";
    }

    private function generateTravelCaption($context) {
        $placeName = $this->contextPlaceName($context);
        return ($placeName ? "{$placeName} trong ký ức của mình: " : "")
            . "một ngày đi qua thật nhẹ, có ảnh đẹp, có vài điều muốn nhớ, và có thêm một dấu ghim trên bản đồ riêng.\n"
            . "#TravelMemory #HanhTrinhCuaToi #Checkin #LuuGiuKyUc";
    }

    private function generateTravelStoryPrompt($context) {
        $placeName = $this->contextPlaceName($context) ?: 'nơi này';
        return "Hôm nay mình đã đi qua {$placeName}. Có những góc nhỏ tưởng bình thường nhưng khi chụp lại thì thành kỷ niệm. Mình muốn lưu ngày này bằng vài tấm ảnh, một mood thật đúng, và một dòng note để sau này mở lại vẫn nhớ cảm giác lúc ấy.";
    }

    private function fetchNominatimPlace($destination) {
        $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&accept-language=vi&q=' . rawurlencode($destination);
        $response = $this->httpGet($url);
        if (!$response) return null;
        $data = json_decode($response, true);
        return !empty($data[0]) ? $data[0] : null;
    }

    private function fetchWikipediaSummary($destination) {
        $searchUrl = 'https://vi.wikipedia.org/w/api.php?action=query&list=search&format=json&srlimit=1&srsearch=' . rawurlencode($destination);
        $searchResponse = $this->httpGet($searchUrl);
        if (!$searchResponse) return null;
        $searchData = json_decode($searchResponse, true);
        $title = $searchData['query']['search'][0]['title'] ?? null;
        if (!$title) return null;

        $summaryUrl = 'https://vi.wikipedia.org/api/rest_v1/page/summary/' . rawurlencode($title);
        $summaryResponse = $this->httpGet($summaryUrl);
        if (!$summaryResponse) return null;
        $summaryData = json_decode($summaryResponse, true);
        $extract = $summaryData['extract'] ?? null;
        if (!$extract) return null;

        return $this->limitText($extract, 320);
    }

    private function fetchOverpassPois($lat, $lon) {
        $radius = 7000;
        $query = '[out:json][timeout:8];('
            . 'node["tourism"~"attraction|viewpoint|museum|gallery|zoo|theme_park"](around:' . $radius . ',' . $lat . ',' . $lon . ');'
            . 'node["historic"](around:' . $radius . ',' . $lat . ',' . $lon . ');'
            . 'node["leisure"~"park|garden"](around:' . $radius . ',' . $lat . ',' . $lon . ');'
            . 'node["amenity"~"restaurant|cafe|fast_food|food_court"](around:' . $radius . ',' . $lat . ',' . $lon . ');'
            . 'way["tourism"~"attraction|viewpoint|museum|gallery"](around:' . $radius . ',' . $lat . ',' . $lon . ');'
            . 'way["leisure"~"park|garden"](around:' . $radius . ',' . $lat . ',' . $lon . ');'
            . ');out center tags 40;';

        $response = $this->httpGet('https://overpass-api.de/api/interpreter?data=' . rawurlencode($query), 10);
        if (!$response) return [];

        $data = json_decode($response, true);
        $pois = [
            'attractions' => [],
            'food' => [],
            'cafes' => [],
            'parks' => [],
            'culture' => []
        ];

        foreach (($data['elements'] ?? []) as $element) {
            $tags = $element['tags'] ?? [];
            $name = $tags['name:vi'] ?? $tags['name'] ?? null;
            if (!$name || strlen($name) < 2) continue;

            if (($tags['amenity'] ?? '') === 'cafe') {
                $pois['cafes'][] = $name;
            } elseif (in_array(($tags['amenity'] ?? ''), ['restaurant', 'fast_food', 'food_court'], true)) {
                $pois['food'][] = $name;
            } elseif (isset($tags['leisure'])) {
                $pois['parks'][] = $name;
            } elseif (isset($tags['historic']) || in_array(($tags['tourism'] ?? ''), ['museum', 'gallery'], true)) {
                $pois['culture'][] = $name;
            } else {
                $pois['attractions'][] = $name;
            }
        }

        foreach ($pois as $key => $items) {
            $pois[$key] = array_slice(array_values(array_unique($items)), 0, 10);
        }

        return $pois;
    }

    private function extractDestination($question) {
        $q = $this->lower($question);
        $base = $this->getTravelKnowledgeBase();

        foreach ($base as $key => $pack) {
            $aliases = array_merge([$key, $this->lower($pack['name'] ?? '')], $pack['aliases'] ?? []);
            foreach ($aliases as $alias) {
                if ($alias && strpos($q, $this->lower($alias)) !== false) {
                    return $pack['name'];
                }
            }
        }

        $known = [
            'ha long' => 'Hạ Long', 'vung tau' => 'Vũng Tàu', 'hue' => 'Huế', 'hoi an' => 'Hội An',
            'can tho' => 'Cần Thơ', 'sapa' => 'Sa Pa', 'ninh binh' => 'Ninh Bình', 'quy nhon' => 'Quy Nhơn',
            'ho tay' => 'Hồ Tây', 'ha giang' => 'Hà Giang', 'bien hoa' => 'Biên Hòa',
        ];
        foreach ($known as $key => $label) {
            if (strpos($q, $key) !== false) {
                return $label;
            }
        }

        $patterns = [
            '/(?:đi|đến|tới|du lịch|tham quan|ở|tại|tour)\s+([\p{L}\s]{2,40}?)(?:\s+\d|\s*,|\s*\?|\.|$)/iu',
            '/(?:địa điểm|dia diem|thời tiết|thoi tiet|ăn gì|an gi|lịch trình|lich trinh)\s+(?:ở|o|tại|tai)?\s*([\p{L}\s]{2,40}?)(?:\s*,|\s*\?|\.|$)/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $question, $matches)) {
                $place = trim($matches[1]);
                $place = preg_replace('/\s+(có|co|với|voi|cho|trong|và|va|nên|nen|được|duoc).*$/iu', '', $place);
                if ($this->textLength($place) >= 2 && $this->textLength($place) <= 40) {
                    return ucwords($place);
                }
            }
        }

        return null;
    }

    private function resolveCuratedPack($destination) {
        if (!$destination) {
            return null;
        }
        $base = $this->getTravelKnowledgeBase();
        $key = $this->knowledgeKey($destination);
        if (isset($base[$key])) {
            return $base[$key];
        }
        foreach ($base as $pack) {
            if ($this->lower($pack['name'] ?? '') === $this->lower($destination)) {
                return $pack;
            }
        }
        return null;
    }

    private function injectCuratedPois(&$context) {
        $c = $context['curated'];
        if (!$c) {
            return;
        }
        foreach (($c['spots'] ?? []) as $spot) {
            $name = preg_replace('/\s*\(.*$/', '', $spot);
            $context['pois']['attractions'][] = $name;
        }
        foreach (($c['food'] ?? []) as $food) {
            $context['pois']['food'][] = $food;
        }
        foreach ($context['pois'] as $k => $items) {
            $context['pois'][$k] = array_values(array_unique($items));
        }
    }

    private function reverseGeocode($lat, $lon) {
        $url = 'https://nominatim.openstreetmap.org/reverse?format=json&accept-language=vi&lat='
            . rawurlencode($lat) . '&lon=' . rawurlencode($lon);
        $response = $this->httpGet($url, 6);
        if (!$response) {
            return null;
        }
        $data = json_decode($response, true);
        $city = $data['address']['city']
            ?? $data['address']['town']
            ?? $data['address']['county']
            ?? $data['address']['state']
            ?? null;
        return $city ? trim($city) : null;
    }

    private function extractDays($question) {
        if (preg_match('/(\d+)\s*ngày/iu', $question, $matches) || preg_match('/(\d+)\s*ngay/iu', $question, $matches)) {
            return intval($matches[1]);
        }
        return 1;
    }

    private function buildPrompt($question, $latitude, $longitude, $context) {
        $prompt = "Câu hỏi người dùng: {$question}";

        if ($latitude && $longitude) {
            $prompt .= "\nVị trí GPS hiện tại: {$latitude}, {$longitude}.";
        }
        if (!empty($context['display_name'])) {
            $prompt .= "\nĐịa điểm nhận diện: " . $context['display_name'];
        }
        if (!empty($context['destination'])) {
            $prompt .= "\nĐiểm đến trong câu hỏi: " . $context['destination'];
        }
        if (!empty($context['days']) && $context['days'] > 1) {
            $prompt .= "\nSố ngày dự kiến: " . intval($context['days']);
        }
        if (!empty($context['summary'])) {
            $prompt .= "\nWikipedia: " . $context['summary'];
        }
        if (!empty($context['cached_knowledge']['tips'])) {
            $prompt .= "\nKiến thức đã tích lũy: " . implode(' | ', $context['cached_knowledge']['tips']);
        }
        if (!empty($context['user_journeys'])) {
            $journeyLines = array_map(function ($j) {
                return ($j['place_name'] ?? '') . ' (' . ($j['feeling'] ?? '') . ', ' . ($j['visit_date'] ?? '') . ')';
            }, array_slice($context['user_journeys'], 0, 8));
            $prompt .= "\nHành trình đã lưu của user: " . implode('; ', $journeyLines);
        }

        $poiText = $this->formatPoiContext($context['pois']);
        if ($poiText) {
            $prompt .= "\nPOI nguồn mở:\n" . $poiText;
        }

        if (!empty($context['weather']['days'])) {
            $prompt .= "\nDự báo thời tiết:\n" . $this->generateWeatherForecast($context);
        }

        if (!empty($context['sources'])) {
            $prompt .= "\nNguồn: " . implode(', ', $context['sources']);
        }

        $intents = $this->detectIntents($this->lower($question));
        if (!empty($context['curated']['name'])) {
            $prompt .= "\nGói kiến thức nội bộ: " . ($context['curated']['intro'] ?? '');
        }

        if (!empty($context['chat_history'])) {
            $prompt .= "\nLịch sử hội thoại gần đây:";
            foreach (array_slice($context['chat_history'], -3) as $turn) {
                $prompt .= "\n- User: " . ($turn['q'] ?? '');
                $prompt .= "\n- AI: " . ($turn['a'] ?? '');
            }
        }

        $scores = $this->scoreIntents($this->lower($question));
        arsort($scores);
        $prompt .= "\nÝ định ưu tiên: " . implode(', ', array_keys(array_slice($scores, 0, 3)));

        $prompt .= "\nTrả lời tiếng Việt tự nhiên, cụ thể, có phán đoán; ưu tiên tên địa điểm trong ngữ cảnh; không trả lời chung chung.";
        return $prompt;
    }

    private function formatPoiContext($pois) {
        $labels = [
            'attractions' => 'Tham quan/check-in',
            'culture' => 'Văn hóa/lịch sử',
            'parks' => 'Công viên/cảnh quan',
            'food' => 'Quán ăn',
            'cafes' => 'Cafe'
        ];
        $lines = [];
        foreach ($labels as $key => $label) {
            if (!empty($pois[$key])) {
                $lines[] = $label . ': ' . implode(', ', array_slice($pois[$key], 0, 8));
            }
        }
        return implode("\n", $lines);
    }

    private function mergePoiArrays($base, $extra) {
        foreach ($extra as $group => $items) {
            if (!isset($base[$group])) {
                $base[$group] = [];
            }
            $base[$group] = array_values(array_unique(array_merge($base[$group], $items)));
        }
        return $base;
    }

    private function pickPois($context, $groups, $limit) {
        $items = [];
        foreach ($groups as $group) {
            $items = array_merge($items, $context['pois'][$group] ?? []);
        }
        return array_slice(array_values(array_unique($items)), 0, $limit);
    }

    private function poiAt($items, $index) {
        if (empty($items)) return null;
        return $items[$index % count($items)] ?? null;
    }

    private function contextPlaceName($context) {
        if (!empty($context['destination'])) return $context['destination'];
        if (!empty($context['display_name'])) return explode(',', $context['display_name'])[0];
        return null;
    }

    private function containsAny($text, $needles) {
        foreach ($needles as $needle) {
            if (strpos($text, $needle) !== false) {
                return true;
            }
        }
        return false;
    }

    private function lower($text) {
        return function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    }

    private function limitText($text, $limit) {
        if ($this->textLength($text) <= $limit) return $text;
        return function_exists('mb_substr') ? mb_substr($text, 0, $limit, 'UTF-8') . '...' : substr($text, 0, $limit) . '...';
    }

    private function textLength($text) {
        return function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    }

    private function httpGet($url, $timeout = 7) {
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_USERAGENT, 'TravelMemoryMap/1.0 (local app)');
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: TravelMemoryMap/1.0 (local app)\r\n",
                'timeout' => $timeout
            ]
        ]);

        return @file_get_contents($url, false, $context);
    }

    private function httpPost($url, $body, $headers = []) {
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers) . "\r\n",
                'content' => $body,
                'timeout' => 20
            ]
        ]);

        return @file_get_contents($url, false, $context);
    }

    private function detectRoutePoints($question) {
        $q = $this->lower($question);
        $start = null;
        $end = null;

        if (preg_match('/từ\s+([\p{L}\s]{2,20}?)\s+(?:đi|đến|tới|ra|vào)\s+([\p{L}\s]{2,20}?)(?:\s+bằng|\s+như|\s*\?|\.|$)/iu', $question, $matches)) {
            $start = trim($matches[1]);
            $end = trim($matches[2]);
        } elseif (preg_match('/lộ\s+trình\s+([\p{L}\s]{2,20}?)\s*-\s*([\p{L}\s]{2,20}?)(?:\s*\?|\.|$)/iu', $question, $matches)) {
            $start = trim($matches[1]);
            $end = trim($matches[2]);
        } elseif (preg_match('/đi\s+([\p{L}\s]{2,20}?)\s+từ\s+([\p{L}\s]{2,20}?)(?:\s+bằng|\s+như|\s*\?|\.|$)/iu', $question, $matches)) {
            $end = trim($matches[1]);
            $start = trim($matches[2]);
        }

        $cleanup = function($str) {
            if (!$str) return null;
            $str = preg_replace('/\s+(bằng|như|khoảng|mất|xe|máy|tàu|phượt|lịch|cách|tại|đi|bao|nhiêu|nào|đầu|cuối|nhanh|chậm|tiện|loại|hợp|vibe|với|ở|tới|đến|ra|vào).*$/iu', '', $str);
            return ucwords(trim($str));
        };

        return [
            'start' => $cleanup($start),
            'end' => $cleanup($end)
        ];
    }

    private function composeRouteAnswer($start, $end, $context) {
        $key = $this->lower($start) . ' - ' . $this->lower($end);
        $keyRev = $this->lower($end) . ' - ' . $this->lower($start);

        $routesDb = [
            'hà nội - đà nẵng' => [
                'dist' => 'khoảng 760 km',
                'plane' => '1 giờ 20 phút (Bay từ Nội Bài đến sân bay Đà Nẵng)',
                'train' => '15 - 17 tiếng (Các chuyến tàu SE chạy dọc Bắc - Nam)',
                'coach' => '14 - 16 tiếng (Xuất phát từ bến xe Giáp Bát, Mỹ Đình hoặc Nước Ngầm)',
                'motorbike' => 'Đi dọc Quốc lộ 1A hoặc Đường Hồ Chí Minh qua dãy Trường Sơn. Nên chia lộ trình thành 3 ngày, dừng chân nghỉ ngơi và check-in tại Nghệ An, Quảng Bình (Phong Nha), và Huế (vượt đèo Hải Vân kỳ vĩ).'
            ],
            'hà nội - sa pa' => [
                'dist' => 'khoảng 320 km',
                'plane' => 'Không có đường bay thẳng (chỉ có thể bay đến Nội Bài rồi đi xe đường bộ)',
                'train' => 'Khoảng 8 tiếng đi tàu hỏa đêm từ ga Hà Nội đến ga Lào Cai, sau đó bắt xe buýt hoặc taxi khoảng 1 tiếng lên thị trấn Sa Pa',
                'coach' => '5 - 6 tiếng đi xe khách giường nằm hoặc xe cabin limousine chạy đường cao tốc Nội Bài - Lào Cai cực kỳ êm ái',
                'motorbike' => 'Cung đường phượt khoảng 7 - 8 tiếng qua Yên Bái hoặc theo hướng Nghĩa Lộ - Mù Cang Chải vượt đèo Khau Phạ trước khi sang Sa Pa.'
            ],
            'hà nội - hà giang' => [
                'dist' => 'khoảng 300 km',
                'plane' => 'Không có sân bay (chỉ di chuyển đường bộ)',
                'train' => 'Không có tuyến tàu hỏa lên Hà Giang',
                'coach' => '6 - 7 tiếng đi xe giường nằm đêm từ Mỹ Đình. Nên đi xe đêm để sáng sớm tới TP. Hà Giang',
                'motorbike' => 'Phượt xe máy khoảng 7 - 8 tiếng theo QL2. Hoặc phương án an toàn nhất: đi xe khách lên TP. Hà Giang rồi thuê xe máy tại đó để chinh phục cung Loop (Quản Bạ - Yên Minh - Đồng Văn - Mèo Vạc).'
            ],
            'sài gòn - đà lạt' => [
                'dist' => 'khoảng 310 km',
                'plane' => '50 phút bay từ Tân Sơn Nhất đến sân bay Liên Khương, sau đó đi xe buýt sân bay 45 phút vào trung tâm Đà Lạt',
                'train' => 'Không có tuyến tàu hỏa chạy thẳng từ Sài Gòn lên Đà Lạt (chỉ có ga Trại Mát chạy tham quan ngắn)',
                'coach' => '6 - 8 tiếng đi xe giường nằm cao cấp (Thành Bưởi, Phương Trang, Nguyễn Kim) leo đèo Bảo Lộc',
                'motorbike' => 'Khoảng 7 - 9 tiếng đi xe máy qua ngả Đồng Nai, leo đèo Bảo Lộc và đèo Prenn. Đoạn đèo Bảo Lộc cần đi cẩn thận vì lưu lượng xe lớn.'
            ],
            'sài gòn - vũng tàu' => [
                'dist' => 'khoảng 120 km',
                'plane' => 'Không di chuyển bằng máy bay',
                'train' => 'Không có tuyến tàu hỏa',
                'coach' => '2 - 2.5 tiếng đi xe limousine đón tận nơi (Hoa Mai, Toàn Thắng, Anh Quốc)',
                'motorbike' => '2.5 - 3 tiếng di chuyển bằng xe máy qua phà Cát Lái sang Nhơn Trạch rồi đi dọc QL51, hoặc đi đường xa lộ Hà Nội.'
            ],
            'sài gòn - nha trang' => [
                'dist' => 'khoảng 430 km',
                'plane' => '1 giờ bay từ Tân Sơn Nhất đến sân bay Cam Ranh, sau đó đi taxi hoặc xe buýt 35km vào TP. Nha Trang',
                'train' => '8 tiếng đi tàu hỏa (có chuyến tàu 5 sao Sài Gòn - Nha Trang chạy đêm rất thoải mái)',
                'coach' => '8 - 9 tiếng đi xe giường nằm chạy dọc Quốc lộ 1A',
                'motorbike' => 'Hành trình phượt ven biển tuyệt đẹp khoảng 9 - 10 tiếng qua Phan Thiết (Mũi Né), Phan Rang (vịnh Vĩnh Hy) rồi tới Nha Trang.'
            ],
            'sài gòn - cần thơ' => [
                'dist' => 'khoảng 170 km',
                'plane' => 'Không bay trực tiếp giữa 2 thành phố gần nhau này',
                'train' => 'Không có tuyến tàu hỏa miền Tây',
                'coach' => '3 - 4 tiếng đi xe khách qua cao tốc Trung Lương - Mỹ Thuận cực nhanh',
                'motorbike' => 'Khoảng 4 - 5 tiếng đi xe máy dọc QL1A cũ qua Long An, Tiền Giang, Vĩnh Long.'
            ]
        ];

        $route = null;
        foreach ($routesDb as $rKey => $rVal) {
            if (strpos($key, $rKey) !== false || strpos($keyRev, $rKey) !== false) {
                $route = $rVal;
                break;
            }
        }

        $name = $_SESSION['full_name'] ?? 'bạn';
        $out = "🧭 **Travel Memory AI — Hướng Dẫn Lộ Trình** 🧭\n\nChào {$name}! Mình đã phân tích hành trình di chuyển từ **{$start}** đi **{$end}**.\n\n";

        if ($route) {
            $out .= "📏 **Khoảng cách di chuyển:** {$route['dist']}\n\n";
            $out .= "🚗 **Các phương tiện di chuyển gợi ý:**\n";
            if ($route['plane'] !== 'Không di chuyển bằng máy bay' && strpos($route['plane'], 'Không có') === false) {
                $out .= "✈️ **Máy bay:** {$route['plane']}\n";
            }
            if ($route['train'] !== 'Không có tuyến tàu hỏa' && strpos($route['train'], 'Không có') === false) {
                $out .= "🚂 **Tàu hỏa:** {$route['train']}\n";
            }
            $out .= "🚌 **Xe khách:** {$route['coach']}\n";
            $out .= "🏍️ **Phượt xe máy:** {$route['motorbike']}\n\n";
        } else {
            $out .= "📏 **Khoảng cách & Hướng di chuyển:** Đây là cung đường kết nối giữa hai địa danh thuộc mạng lưới du lịch Việt Nam.\n\n";
            $out .= "🚗 **Gợi ý phương tiện phổ thông:**\n";
            $out .= "✈️ **Máy bay:** Kiểm tra xem {$end} (hoặc thành phố lân cận) có sân bay thương mại hay không. Nếu có, bay là giải pháp nhanh nhất.\n";
            $out .= "🚌 **Xe giường nằm / Limousine:** Luôn có các chuyến xe khách chạy liên tỉnh chạy ngày/đêm. Bạn nên đặt xe trước 1-2 ngày.\n";
            $out .= "🏍️ **Xe máy (Phượt tự túc):** Phù hợp với cung đường dưới 300km hoặc các bạn đam mê chinh phục thử thách. Hãy bảo dưỡng xe tốt, chuẩn bị bản đồ offline và đồ bảo hộ đầy đủ trước khi lên đường.\n\n";
        }

        $out .= "💡 **Tính năng bổ trợ từ Travel Memory Map:**\n";
        $out .= "1. Hãy tạo một **Chuyến đi mới** trên website với tên `\"Hành trình từ {$start} đi {$end}\"` để bắt đầu lưu trữ.\n";
        $out .= "2. Khi di chuyển, cứ mỗi điểm dừng chân ăn uống hoặc chụp ảnh đẹp, bạn hãy **Thêm Check-in** mới vào chuyến đi đó trên web. Bản đồ sẽ tự động vẽ đường lộ trình kiến bò chuyển động ('Marching Ants') kết nối các kỷ niệm của bạn thành một thước phim sống động!\n";
        $out .= "3. Đừng quên mở tab **Hồ sơ** để xem các huy chương hành trình bạn đã mở khóa nhé! Chúc bạn có một hành trình an toàn và đầy ắp kỷ niệm đẹp!";

        return $out;
    }
}
?>

import codecs

with open("edit_report_v2.ps1", "r", encoding="utf-8") as f:
    content = f.read()

with codecs.open("edit_report_v2.ps1", "w", encoding="utf-8-sig") as f:
    f.write(content)

print("Converted edit_report_v2.ps1 to UTF-8 with BOM!")

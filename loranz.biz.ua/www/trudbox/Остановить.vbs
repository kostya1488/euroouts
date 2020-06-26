Set WshShell = WScript.CreateObject("WScript.Shell")
WshShell.Run"taskkill /IM TrudBot_v2.4.exe", 0
WshShell.Run"taskkill /IM phantomjs.exe", 0
WshShell.Run"taskkill /IM wscript.exe", 0
WshShell.Run"taskkill /IM tesseract.exe", 0
Set WshShell = WScript.CreateObject("WScript.Shell")
Dim InputName
Dim i
InputName = InputBox("Bведите количество запускаемых копий бота:")


Do While (i<Int(InputName))
	wscript.sleep 3000
	WshShell.Run"TrudBot_v2.4.exe", 0, false
	i = i + 1
loop

WScript.Quit
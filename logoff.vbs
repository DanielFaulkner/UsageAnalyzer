' This script collects information on the computer and user executing it and sends it to a webpage to process

On Error Resume Next

' ********** Script variables: **********

ServerAddress = "http://192.168.0.1/UsageAnalyzer/recorduser.php"
ConnectionAttempts = 1
ErrorLogLocation = "N:\My Settings\LastTrackerError.txt"


' ********** Program code ***********


' *** Detect the system info
Set objWMIService = GetObject("winmgmts:\\.\root\cimv2")
Set objComputer = objWMIService.ExecQuery("Select * from Win32_ComputerSystem")
For Each item in objComputer
	pcUserName = item.UserName
	pcName = item.Name
Next

' *** Send information to website form, allow for 3 retry's if the server doesn't respond
Counter = 0

Do

' * Send information to the server
set objHTTP = CreateObject("MSXML2.ServerXMLHTTP")
'("Microsoft.XMLHTTP")
objHTTP.open "POST", ServerAddress, False
objHTTP.setRequestHeader "Content-Type", "application/x-www-form-urlencoded"
objHTTP.send "status=off&user="&pcUserName&"&location="&pcName
'MsgBox objHTTP.responseText
set objHTTP = Nothing

' * Check the to see if the upload worked.
If Err.Number = 0 Then
	Finished = 1
Else
	Finished = 0
	Counter = Counter + 1
End If

' * Check to see if the upload has failed 3 times
If Counter > ConnectionAttempts Then
	Finished = 1
	Counter = Counter - 1

	' * Create a log file in the users area.
	set objFS = CreateObject("Scripting.FileSystemObject")
	set objNewFile = objFS.CreateTextFile(ErrorLogLocation)
	objNewFile.WriteLine "Logoff Tracking Error (After "&Counter&" attempts)"
	objNewFile.WriteLine "Computer: "&pcName
	objNewFile.WriteLine "Error: "&Err
	objNewFile.WriteLine "Source: "&Err.Source
	objNewFile.WriteLine "Description: "&Err.Description
	objNewFile.Close
End If

Loop Until Finished =1

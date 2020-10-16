' This script collects information on the computer and user executing it and sends it to a webpage to process

On Error Resume Next

' ********** Script variables: **********

LoginServerAddress = "http://192.168.0.1/UsageAnalyzer/recorduser.php"
AuditServerAddress = "http://192.168.0.1/UsageAnalyzer/recordaudit.php"
BenchmarkServerAddress = "http://192.168.0.1/UsageAnalyzer/recordbenchmark.php"
ConnectionAttempts = 3
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
objHTTP.open "POST", LoginServerAddress, False
objHTTP.setRequestHeader "Content-Type", "application/x-www-form-urlencoded"
objHTTP.send "status=on&user="&pcUserName&"&location="&pcName
'MsgBox objHTTP.responseText
ServerResponse = objHTTP.responseText
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
	objNewFile.WriteLine "Logon Tracking Error (After "&Counter&" attempts)"
	objNewFile.WriteLine "Computer: "&pcName
	objNewFile.WriteLine "Error: "&Err
	objNewFile.WriteLine "Source: "&Err.Source
	objNewFile.WriteLine "Description: "&Err.Description
	objNewFile.Close
End If

Loop Until Finished =1

' **************************************************************************************************
' *********************************** Check Server response - options are: *************************
' ****** 000 - Nothing further required (or just don't send a compatible response)
' ****** 100 - Run a system audit
' ****** 010 - Send benchmark data
' ****** 001 - Run benchmarks
' ****** 002 - Only run benchmarks if no valid benchmarks stored on client
' ****** (Each column is independant so 112 will run an audit check for benchmark data and send it )
' **************************************************************************************************


' *** Check server response to see if more action is required
If Len(ServerResponse) > 2 Then
	'MsgBox "Checking server response"
	audit = Mid(ServerResponse,1,1)
	getbenchmark = Mid(ServerResponse,2,1)
	runbenchmark = Mid(ServerResponse,3,1)

	' * Perform audit
	If audit > 0 Then
'************************************************ AUDIT SECTION **************************************
' *** Detect system information ***

Set objWMIService = GetObject("winmgmts:\\.\root\cimv2")

Set objComputer = objWMIService.ExecQuery("Select * from Win32_ComputerSystem")

For Each item in objComputer

	pcUserName = item.UserName

	pcName = item.Name

	pcModel = item.Model

	pcManufacturer = item.Manufacturer

	pcRamSize = item.TotalPhysicalMemory

	pcStatus = item.Status

	pcThermalState = item.ThermalState
Next


Set objOperating = objWMIService.ExecQuery("Select * from Win32_OperatingSystem")

For Each item in objOperating

	pcCaption = item.Caption

	pcServicePackVersion = item.ServicePackMajorVersion

	pcServicePack = item.CSDVersion

	pcSerialNumber = item.SerialNumber

Next



Set objDisk = objWMIService.ExecQuery("Select * from Win32_DiskDrive")

For Each item in objDisk

	pcDiskSize = item.Size

	pcDiskStatus = item.Status

	pcDiskType = item.MediaType

Next



Set objProcessor = objWMIService.ExecQuery("Select * from Win32_Processor")

For Each item in objProcessor

	pcCPUMaxClockSpeed = item.MaxClockSpeed

	pcCPUNumberOfCores = item.NumberOfCores

	pcCPUManufacturer = item.Manufacturer

Next



Set objBIOS = objWMIService.ExecQuery("Select * from Win32_BIOS")

For Each item in objBIOS

	pcBIOSSerialNumber = item.SerialNumber

Next



Set objBaseBoard = objWMIService.ExecQuery("Select * from Win32_BaseBoard")

For Each item in objBaseBoard

	pcBaseBoardSerialNumber = item.SerialNumber

Next



Set objComputerSystemProduct = objWMIService.ExecQuery("Select * from Win32_ComputerSystemProduct")

For Each item in objComputerSystemProduct

	pcComputerSystemIDNum = item.IdentifyingNumber

	pcComputerSystemVendor = item.Vendor

Next


Set colDisks = objWMIService.ExecQuery("Select * from Win32_LogicalDisk Where DeviceID = 'C:'")
For Each objDisk in colDisks
	pcHDDFreeSpace = objDisk.FreeSpace
	' Can also be used to get other information on a partition
Next


' * Send information to the server
set objHTTP = CreateObject("MSXML2.ServerXMLHTTP")
'("Microsoft.XMLHTTP")
objHTTP.open "POST", AuditServerAddress, False
objHTTP.setRequestHeader "Content-Type", "application/x-www-form-urlencoded"
'MsgBox "Sending: "&"computer="&pcName&"&make="&pcManufacturer&"&model="&pcModel&"&snsys="&pcComputerSystemIDNum&"&snbios="&pcBIOSSerialNumber&"&snmb="&pcBaseBoardSerialNumber&"&os="&pcCaption&pcServicePack&"&ram="&pcRamSize/1000000&"&hdd="&pcDiskSize/1000000000&"&cpumake="&pcCPUManufacturer&"&cpuspeed="&pcCPUMaxClockSpeed&"&cpucores="&pcCPUNumberOfCores
objHTTP.send "computer="&pcName&"&make="&pcManufacturer&"&model="&pcModel&"&snsys="&pcComputerSystemIDNum&"&snbios="&pcBIOSSerialNumber&"&snmb="&pcBaseBoardSerialNumber&"&os="&pcCaption&pcServicePack&"&ram="&pcRamSize/1000000&"&hdd="&pcDiskSize/1000000000&"&hddfree="&pcHDDFreeSpace/1000000000&"&cpumake="&pcCPUManufacturer&"&cpuspeed="&pcCPUMaxClockSpeed&"&cpucores="&pcCPUNumberOfCores
'MsgBox objHTTP.responseText
set objHTTP = Nothing

' ********************************************** END AUDIT SECION **************************************		
	End If

	' * If runbenchmark is set to 2 then only run the benchmark if it's needed
	If runbenchmark > 1 Then
' ******************************************** SMART BENCHMARK SECTION *********************************
'Msgbox "Testing if benchmarks are upto date"
Set objWMIService = GetObject("winmgmts:\\.\root\cimv2")

Set colWSA = objWMIService.ExecQuery("Select * From Win32_WinSAT") 

For Each objItem in colWSA 
    weiState = objItem.WinSATAssessmentState 
next

' States are: 0-unknown,1-valid,2-NeedsUpdating,3-NotAvailable,4-Invalid
If weiState = 1 Then
	runbenchmark = 0
End If
' ****************************************** END SMART BENCHMARK SECTION *******************************
	End If
	' * Run benchmark assessment
	If runbenchmark > 0 Then
' ********************************************** RUN BENCHMARK SECTION *********************************
		'MsgBox "Starting benchmark your PC may run slower than normal during this process"
		Set WshShell = CreateObject("Wscript.Shell")
		' Options are: program to run, display window, wait for the program to end
		WshShell.Run "winsat prepop",0,true
		'MsgBox "Benchmark Finished"
' *********************************************END RUN BENCHMARK SECTION *******************************
	End If

	' * Perform benchmark
	If getbenchmark > 0 Then
' *********************************************** BENCHMARK SECTION ************************************
' *** Detect system performance ***

Set objWMIService = GetObject("winmgmts:\\.\root\cimv2")

Set colWSA = objWMIService.ExecQuery("Select * From Win32_WinSAT") 

For Each objItem in colWSA 
    weiCPU = objItem.CPUScore 
    WeiRAM = objItem.MemoryScore 
    weiGPU = objItem.GraphicsScore 
    weiGPU3d = objItem.D3DScore 
    weiHDD = objItem.DiskScore 
    weiBase = objItem.WinSPRLevel
    weiState = objItem.WinSATAssessmentState
next

' * Send information to the server
set objHTTP = CreateObject("MSXML2.ServerXMLHTTP")
'("Microsoft.XMLHTTP")
objHTTP.open "POST", BenchmarkServerAddress, False
objHTTP.setRequestHeader "Content-Type", "application/x-www-form-urlencoded"
objHTTP.send "computer="&pcName&"&cpu="&weiCPU&"&ram="&weiRAM&"&gpu="&weiGPU&"&gpu3d="&weiGPU3d&"&hdd="&weiHDD&"&base="&weiBase&"&state="&weiState
'MsgBox objHTTP.responseText
set objHTTP = Nothing
' ********************************************* END BENCHMARK SECTION **********************************
	End If

End If

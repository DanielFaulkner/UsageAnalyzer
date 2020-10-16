' This script will record information on the computer to a CSV file.
' You may wish to start the CSV file with the headers before rolling out this login script.
' The information recorded is: Date,Domain Name,Username,Make,Model,Computer S/N,BIOS S/N,Motherboard S/N,OS,RAM,HDD and Processor

On Error Resume Next

' ********** Variables **********

' * Filename to save the information to
strFile = "AuditTable.csv"
' * Delay the executing of the script so that network drives can be mapped first
WScript.Sleep(300000) ' In milliseconds - 1000 = 1 second

' ********** Program code **********

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


' *** Display information ***
'MsgBox "Make: "&pcComputerSystemVendor&"/"&pcManufacturer&vbCrLf&_
'	"Model: "&pcModel&vbCrLf&_
'	"Serial Number: "&pcBIOSSerialNumber&" / "&pcComputerSystemIDNum&" / "&pcBaseBoardSerialNumber&vbCrLf&_
'	"Operating System: "&pcCaption&" "&pcServicePack&vbCrLf&_
'	"Processor: "&pcCPUManufacturer&" "&pcCPUMaxClockSpeed&"Mhz "&pcCPUNumberOfCores&" Core(s)"&vbCrLf&_
'	"Memory: "&pcRamSize/1000000&"Mb"&vbCrLf

' *** Write information to file ***
today = Date
set objFS = CreateObject("Scripting.FileSystemObject")

' Check if file exists
If objFS.FileExists(strFile) = 0 Then
	Set objFile = objFS.CreateTextFile(strFile)
End If

' Append = 8, Read = 1, Write =2
Const Append = 8
Set objTextFile = objFS.OpenTextFile(strFile, Append, True)

objTextFile.WriteLine(Day(today)&"/"&Month(today)&"/"&Year(today)&","&pcName&","&pcUserName&","&pcManufacturer&","&pcModel&","&pcComputerSystemIDNum&","&pcBIOSSerialNumber&","&pcBaseBoardSerialNumber&","&pcCaption&pcServicePack&","&pcRamSize/1000000&"Mb"&","&pcDiskSize/1000000000&"Gb "&pcDiskType&","&pcCPUManufacturer&" "&pcCPUMaxClockSpeed&"Mhz "&pcCPUNumberOfCores&" Core(s)")
objTextFile.Close
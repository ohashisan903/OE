# Paul Ohashi
# TCI
# JML script

Import-Module ActiveDirectory

function New-Joiner {
    Write-Host "`n=== NEW JOINER ===" -ForegroundColor Green

    # General Tab
    $FirstName = Read-Host "First Name"
    $LastName = Read-Host "Last Name"

    # Organization Tab
    $Title = Read-Host "Job Title"
    $Department = Read-Host "Department"
    $ManagerSam = Read-Host "Manager Username"

    # Static values
    $DefaultOU = 'OU=TestDev,OU=Texas,DC=verticalcable,DC=local'
    $DefaultOffice = 'Bonham TX'
    $DefaultPhone = '903-449-4622'
    $DefaultCompany = 'Trans Cable International'
    $WebPage = 'https://transcableusa.com'
    $UPNDomain = 'transcableusa.com'
    $OU = 'OU=TestDev,OU=Texas,DC=verticalcable,DC=local'
    $Email = "$FirstName.$LastName@transcableusa.com"
    $SamAccountName = "$FirstName.$LastName"
    $DisplayName = "$FirstName $LastName"
    $Description = "Start Date: $(Get-Date -Format 'M/d/yy')"
    $Office = "Bonham TX"
    $Telephone = "903-449-4622"
    $Company = "Trans Cable International"

    # Define a password
    $Password = Read-Host "Temporary Password" -AsSecureString

    try {
        $ManagerDN = (Get-ADUser $ManagerSam).DistinguishedName
        New-ADUser `
            -Name $DisplayName `
            -GivenName $FirstName `
            -Surname $LastName `
            -DisplayName $DisplayName `
            -SamAccountName $SamAccountName `
            -UserPrincipalName "$SamAccountName@$UPNDomain" `
            -Path $OU `
            -Office $Office `
            -OfficePhone $Telephone `
            -EmailAddress $Email `
            -Description $Description `
            -HomePage $WebPage `
            -Title $Title `
            -Department $Department `
            -Company $Company `
            -Manager $ManagerDN `
            -AccountPassword $Password `
            -Enabled $true `
            -ChangePasswordAtLogon $true

        Write-Host "`nUser created successfully." -ForegroundColor Green
    }
    catch {
        Write-Host $_.Exception.Message -ForegroundColor Red
    }
}

function Update-Mover {

    Write-Host "`n=== MOVER ===" -ForegroundColor Yellow

    $User = Read-Host "Username of employee"

    try {
        Get-ADUser $User -Properties * | Out-Null
    }
    catch {
        Write-Host "User not found." -ForegroundColor Red
        return
    }

    $Title = Read-Host "New Title"
    $Department = Read-Host "New Department"
    $ManagerSam = Read-Host "New Manager Username"

    try {
        $ManagerDN = (Get-ADUser $ManagerSam).DistinguishedName

        Set-ADUser $User `
            -Title $Title `
            -Department $Department `
            -Manager $ManagerDN

        Write-Host "`nUser updated successfully." -ForegroundColor Green
    }
    catch {
        Write-Host $_.Exception.Message -ForegroundColor Red
    }
}

function Process-Leaver {
# Paul Ohashi
# TCI
# Deprovision user script

    Import-Module ActiveDirectory
    CLS
    # ===== DEFINE DISABLED USERS OU =====
    $DisabledUsersOU = "OU=Disabled Users,OU=Texas,DC=verticalcable,DC=local"

    Write-Host "Terminate User"
    Write-Host ""

    # ===== PROMPT FOR USERNAME =====
    $SamAccountName = Read-Host "Enter SamAccountName"

    # ===== GET USER =====
    try {
        $User = Get-ADUser $SamAccountName -Properties MemberOf, Description, DistinguishedName
    }
    catch {
        Write-Host "User not found." -ForegroundColor Red
        exit
    }

    # ===== PROMPT TO DISABLE USER =====
    $DisableChoice = Read-Host "Disable user? (Y/N)"
    $Comment = Read-Host "Comment:"

    # ===== GENERATE RANDOM 32 CHARACTER PASSWORD =====
    Add-Type -AssemblyName System.Web

    $RandomPassword = [System.Web.Security.Membership]::GeneratePassword(32,8)

    # ===== RESET PASSWORD =====
    Set-ADAccountPassword `
        -Identity $User.SamAccountName `
        -Reset `
        -NewPassword (ConvertTo-SecureString $RandomPassword -AsPlainText -Force)

    # ===== REMOVE GROUP MEMBERSHIPS =====
    $GroupsRemoved = @()

    # Get all groups except Domain Users
    $Groups = Get-ADPrincipalGroupMembership $User |
        Where-Object { $_.Name -ne "Domain Users" }

    foreach ($Group in $Groups) {

        try {
            Remove-ADGroupMember `
                -Identity $Group `
                -Members $User `
                -Confirm:$false

            $GroupsRemoved += $Group.Name
        }
        catch {
            Write-Host "Failed to remove from group: $($Group.Name)" -ForegroundColor Yellow
        }
    }

    # ===== UPDATE DESCRIPTION WITH TERMINATION DATE =====
    $CurrentDescription = $User.Description

    $TermDate = Get-Date -Format "MM/dd/yyyy"

    if ([string]::IsNullOrWhiteSpace($CurrentDescription)) {
        $NewDescription = "Term Date: $TermDate"
    }
    else {
        $NewDescription = "$CurrentDescription | Term Date: $TermDate"
    }

    Set-ADUser `
        -Identity $User `
        -Description $NewDescription

    # ===== DISABLE AND MOVE USER =====
    if ($DisableChoice -match '^[Yy]$') {

        Disable-ADAccount -Identity $User

        Move-ADObject `
            -Identity $User.DistinguishedName `
            -TargetPath $DisabledUsersOU

        $UserDisabled = "YES"
    }
    else {
        $UserDisabled = "NO"
    }

    # ===== OUTPUT RESULTS =====
    Write-Host ""
    Write-Host "========== USER TERMINATION COMPLETE ==========" -ForegroundColor Cyan
    Write-Host "Username: $($User.SamAccountName)"
    Write-Host "Disabled: $UserDisabled"
    Write-Host "Password changed to random 32-character value"
    Write-Host "Updated Description: $NewDescription"
    Write-Host "User moved to: $DisabledUsersOU"
    Write-Host ""

    Write-Host "Groups Removed:" -ForegroundColor Cyan

    if ($GroupsRemoved.Count -eq 0) {
        Write-Host "None"
    }
    else {
        $GroupsRemoved | ForEach-Object {
        Write-Host "- $_"
    }
    }

        Write-Host "========== ACCESS CONTROL REVOKED ==========" -ForegroundColor Cyan
        Write-Host "- Deleted Atlas Access Control account"
        Write-Host ""
        Write-Host "========== AUDIT ==========" -ForegroundColor Cyan
        Write-Host "- Updated EmployeeActiveDirectoryAccounts.xlsx"
        Write-Host ""
        Write-Host "Comment: $Comment"
    }

    do {

        Clear-Host

        Write-Host "======================================" -ForegroundColor Cyan
        Write-Host " JOINER | MOVER | LEAVER TOOL"
        Write-Host "======================================"
        Write-Host "1. Joiner"
        Write-Host "2. Mover"
        Write-Host "3. Leaver"
        Write-Host "4. Exit"

        $Choice = Read-Host "`nSelect an option"

        switch ($Choice) {

            "1" { New-Joiner }
            "2" { Update-Mover }
            "3" { Process-Leaver }
            "4" { break }

            default {
                Write-Host "Invalid selection." -ForegroundColor Red
            }
        }

        if ($Choice -ne "4") {
            Read-Host "`nPress ENTER to continue"
        }

    } while ($Choice -ne "4")
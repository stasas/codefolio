Private Sub Otvori_Click()
On Error Resume Next
Dim Status As Integer
Dim TipUvoza As Byte

    VoziloID = Forms!PonudaGO!VoziloID
    Bau = Me.Baumuster

    If IsNull(VoziloID) Then
        TipUvoza = 1
    Else
        Lok = DLookup("LokacijaID", "Vozilo", "voziloID=" & VoziloID)
        
        If proveriLokaciju(Lok, LagerIzmenljivo) Then
            MsgBox "Opremu ne mozete menjati!"
            Exit Sub
        End If
        If Not Bau = proveriBaumuster(VoziloID) Then
            MsgBox "Nije moguce izvrsiti uvoz, baumuster postojeceg i vozila koje uvozite nije isti!"
            Exit Sub
        End If

        TipUvoza = 2
    End If

    If IsNull(Bau) Then
        MsgBox "Niste preuzeli podatke iz GO-a !", vbCritical, "Upozorenje!"
        Exit Sub
    End If
    If postojiBaumuster(Bau) = 0 Then
        MsgBox "Baumuster ne postoji u bazi obratite se veleprodaji!!!", vbCritical, "Upozorenje"
        Exit Sub
    Else
        If Not ceneVozilaBau(Bau) Then
            MsgBox "Ne postoji Cena Osnovnog Vozila Bez Carine Prava za trazeni baumuster, zovite veleprodaju"
            Exit Sub
        End If
        
        UvozRezultat = GOXMLUvoz(TipUvoza, Me.Baumuster, Me.VoziloID, , Me.Mbksf, Me.NivoEmisije)
        
        If Not IsNull(UvozRezultat) Then
            Me.ProgressBarFG.Width = 3402
            Me.ProgressBarLBL.Caption = "100%"
            DoCmd.OpenForm "Vozilo", , , "[VoziloID] =" & UvozRezultat
            MsgBox "Uvoz vozila gotov!", vbOKOnly
        End If
        
        Forms![PonudaGO]![VoziloID] = Null
        Forms![PonudaGO]![VoziloID].Requery
    End If


Exit_Otvori_Click:
    Exit Sub
Err_Otvori_Click:
    MsgBox Err.Description
    Resume Exit_Otvori_Click
End Sub
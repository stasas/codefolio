Public Function GOXMLUvoz(bTipUvoza As Byte, Optional vBaumuster As Variant, Optional vVoziloID As Variant = Null, Optional vKomisioniBroj As Variant = Null, Optional vXMLFile As Variant = Null, Optional vNivoEmisije As Variant = Null, Optional vKonfigurator As Variant = Null) As Variant

On Error GoTo ErrorHandler
Dim u As String
Dim Standardna As Currency
Dim Dodatna As Currency
Dim Ostalo As Currency
Dim StandardnaN As Currency
Dim DodatnaN As Currency
Dim OstaloN As Currency
Dim rst As DAO.Recordset

Dim ws As Workspace
Dim bm As DAO.Recordset
Dim pp As DAO.Recordset
Dim kola As DAO.Recordset
Dim doq As DAO.Recordset
Dim soq As DAO.Recordset
Dim ooq As DAO.Recordset
Dim u1 As String
Dim paketi As Recordset

Dim brojkodova As Integer
Dim tmpbrojkodova As Integer
Dim paketid As Integer
Dim updpaketid As Integer
Dim paketcena As String
Dim paketkod As String
Dim paketopis As String
Dim imapaket As Boolean
Dim poslednjired As Integer

'novi E klasa paketi (212*)
If bauImaPaket(vBaumuster) Then
    Set paketi = CurrentDb.OpenRecordset("SELECT Paket.PaketID, Paket.NazivPaketa, Paket.Cena, Count(PaketOprema.Kod) AS BrojKodova FROM Paket LEFT JOIN PaketOprema ON PaketOprema.PaketID = Paket.PaketID GROUP BY Paket.PaketID, Paket.NazivPaketa, Paket.Cena")
    paketi.MoveFirst
    Do Until paketi.EOF
        brojkodova = paketi("BrojKodova")
        paketid = paketi("PaketID")
    
        tmpbrojkodova = Nz(DLookup("tempBrojKodova", "QXMLimportPaket", "PaketID = " & paketid), 0)
        
        If brojkodova = tmpbrojkodova Then
            imapaket = True
            updpaketid = paketid
            paketkod = "PAK" & paketid
            paketopis = paketi("NazivPaketa")
            paketcena = paketi("Cena") & ",00"
        End If
        paketi.MoveNext
    Loop
End If
If imapaket Then
    'ubaci novu "dodatnu opremu" tj paket
    CurrentDb.Execute ("INSERT INTO opremaxml (vrsta,kod,naziv,prevod,cena,preveden) VALUES ('P','" & paketkod & "','" & paketopis & "','" & paketopis & "','" & paketcena & "',-1)")
End If

'podaci sa baumustera
Set bm = CurrentDb.OpenRecordset("SELECT QBaumuster.*, QBaumuster.Baumuster From QBaumuster WHERE (((QBaumuster.Baumuster)='" & vBaumuster & "'))")

'dodatna oprema
Set doq = CurrentDb.OpenRecordset("SELECT formatCena([Cena]) AS Iznos, broj FROM opremaxml WHERE vrsta = 'F' or vrsta = 'P' or vrsta = 'L'")
'standardna oprema
Set soq = CurrentDb.OpenRecordset("SELECT formatCena([Cena]) AS Iznos, broj FROM opremaxml WHERE vrsta = 'S'")
'ostala oprema
Set ooq = CurrentDb.OpenRecordset("SELECT formatCena([Cena]) AS Iznos, broj FROM opremaxml WHERE vrsta = 'C' or vrsta = 'T'")

Dodatna = 0
Standardna = 0
Ostalo = 0
DodatnaN = 0
StandardnaN = 0
OstaloN = 0

CenaSO = 0
CenaDO = 0
CenaOO = 0

'zbirna cena iz GO-a (Dodatna) i zbirna cena sa carinom (CenaDO)
If doq.RecordCount > 0 Then
    doq.MoveFirst
    Do Until doq.EOF
    Dodatna = Dodatna + doq("Iznos")
    DodatnaN = DodatnaN + Round(doq("Iznos") * (1 - bm("ENPProcenat") / 100))
    CenaDO = CenaDO + Round(((doq("Iznos") + (IIf(doq("Iznos") = 0, 0, Round(doq("Iznos") * (1 - bm("ENPProcenat") / 100)))) * bm("CarinaProcenat") / 100)) + 0.5)
    CenaDO = Round(CenaDO)
    doq.MoveNext
    Loop
End If

'zbirna cena iz MBKS-a (Standardna) i zbirna cena sa carinom (CenaSO)
If soq.RecordCount > 0 Then
    soq.MoveFirst
    Do Until soq.EOF
    Standardna = Standardna + soq("Iznos")
    StandardnaN = StandardnaN + Round(soq("Iznos") * (1 - bm("ENPProcenat") / 100))
    CenaSO = CenaSO + Round(((soq("Iznos") + (IIf(soq("Iznos") = 0, 0, Round(soq("Iznos") * (1 - bm("ENPProcenat") / 100)))) * bm("CarinaProcenat") / 100)) + 0.5)
    CenaSO = Round(CenaSO)
    soq.MoveNext
    Loop
End If

'zbirna cena iz GO-a (Ostalo) i zbirna cena sa carinom (CenaOO)
If ooq.RecordCount > 0 Then
    ooq.MoveFirst
    Do Until ooq.EOF
    Ostalo = Ostalo + ooq("Iznos")
    OstaloN = OstaloN + Round(ooq("Iznos") * (1 - bm("ENPProcenat") / 100))
    CenaOO = CenaOO + Round(((ooq("Iznos") + (IIf(ooq("Iznos") = 0, 0, Round(ooq("Iznos") * (1 - bm("ENPProcenat") / 100)))) * bm("CarinaProcenat") / 100)) + 0.5)
    CenaOO = Round(CenaOO)
    ooq.MoveNext
    Loop
End If
    
OpremaDodatnaNabavnaCena = IIf(DodatnaN = 0, 0, DodatnaN)
OpremaStandardnaNabavnaCena = IIf(StandardnaN = 0, 0, StandardnaN)
OpremaOstalaNabavnaCena = IIf(OstaloN = 0, 0, OstaloN)
NabavnaVrednostOpreme = IIf(OpremaDodatnaNabavnaCena + OpremaStandardnaNabavnaCena + OpremaOstalaNabavnaCena = 0, 0, OpremaDodatnaNabavnaCena + OpremaStandardnaNabavnaCena + OpremaOstalaNabavnaCena)
CarinaOpreme = NabavnaVrednostOpreme * bm("CarinaProcenat") / 100


If bTipUvoza = 1 Then
    'slobodan VoziloID iz tabele predmetposlovanja
    IDVozilo = Nz(DMax("PredmetPoslovanjaID", "PredmetPoslovanja"), 0) + 1

    Set ws = Workspaces(0)
        ws.BeginTrans
            'Predmet poslovanja
            Set pp = CurrentDb.OpenRecordset("PredmetPoslovanja")
            With pp
                .AddNew
                pp("NazivPredmetaPoslovanja") = DLookup("nazivMarke", "Marka", "MarkaID=" & bm("MarkaID")) + " " + bm("tip")
                pp("PredmetPoslovanjaID") = IDVozilo
                pp("JedinicaMereID") = 1
                pp("VrstaPredmetaPoslovanjaID") = 1
                pp("ZadnjaFakturnaVrednost") = bm("LLP")
                pp("UkupnaCenaSaCarinom") = Round(bm("LLP") + CenaSO + CenaDO + CenaOO)
                pp("otvorio") = Forms!Glavna!FReferentID
                pp("PlanskaCena") = bm("CenaOsnovnogVozilaBezCarinePrava") + Standardna + Dodatna + Ostalo
                pp("DatumOtvorio") = Date
                .Update
            End With
            
            'vozilo
            Set kola = CurrentDb.OpenRecordset("Vozilo")
            With kola
                kola.AddNew
                kola("VoziloID") = IDVozilo
                kola("KomisioniBroj") = vKomisioniBroj
                kola("KodNovoPolovno") = 1
                kola("baumuster") = vBaumuster
                kola("dlp") = bm("dlp")
                kola("dlptransport") = bm("dlptransport")
                kola("fob") = bm("fob")
                kola("ENPProcenat") = bm("ENPProcenat")
                kola("SNL") = bm("SNL")
                kola("KoeficijentOsiguranja") = bm("KoeficijentOsiguranja")
                kola("CenaTransporta") = bm("CenaTransporta")
                kola("CarinaProcenat") = bm("CarinaProcenat")
                kola("BankarskiTrosakProcenat") = bm("BankarskiTrosakProcenat")
                kola("SpediterskiTrosak") = bm("SpediterskiTrosak")
                kola("LLP") = bm("LLP")
                kola("MaloprodajnaMarzaProcenat") = bm("MaloprodajnaMarzaProcenat")
                kola("CenaOsnovnogVozilaBezCarinePrava") = bm("CenaOsnovnogVozilaBezCarinePrava")
                kola("ENP") = bm("ENP")
                kola("PosebanPopust") = bm("PosebanPopust")
                kola("PrvaNabavnaCena") = bm("PrvaNabavnaCena")
                kola("Osiguranje") = bm("Osiguranje")
                kola("CenaCIPBeograd") = bm("CenaCIPBeograd")
                kola("Carina") = bm("Carina")
                kola("BankarskiTrosak") = bm("BankarskiTrosak")
                kola("UkupnaNabavnaCena") = bm("UkupnaNabavnaCena")
                kola("UkupnoNabavnaCenaSaCarinom") = bm("UkupnaNabavnaCenaSaCarinom")
                kola("MaloprodajnaMarza") = bm("MaloprodajnaMarza")
                kola("VeleprodajnaCena") = bm("VeleprodajnaCena")
                kola("VeleProdajnaMarza") = bm("VeleProdajnaMarza")
                kola("VeleProdajnaMarzaProcenat") = bm("VeleProdajnaMarzaProcenat")
                kola("KursnaListaID") = DLookup("[KursnaListaID]", "System")
                kola("MBKSfile") = vXMLFile
                kola("DatumStatusa") = Date
                kola("StatusVozilaID") = 1
                kola("CarinaOpreme") = CarinaOpreme
                kola("cenaopremesacarinom") = CenaSO + CenaDO + CenaOO
                kola("OpremaStandardnaCena") = Standardna
                kola("OpremaDodatnaCena") = Dodatna
                kola("OpremaOstalaCena") = Ostalo
                kola("OpremaStandardnaNabavnaCena") = OpremaStandardnaNabavnaCena
                kola("OpremaDodatnaNabavnaCena") = OpremaDodatnaNabavnaCena
                kola("OpremaOstalaNabavnaCena") = OpremaOstalaNabavnaCena
                kola("NabavnaVrednostOpreme") = NabavnaVrednostOpreme
                kola("DrzavaPorekla") = Nz(bm("DrzavaPorekla"), 276)
                kola("DrzavaProizvodnje") = Nz(bm("DrzavaProizvodnje"), 276)
                'CG vrednosti
                kola("CGSNL") = IIf(Nz(bm("CGSNL"), 0) = 0, 0, bm("CGSNL"))
                kola("CGCarina") = IIf(Nz(bm("CGCarinaProcenat"), 0) = 0, 0, bm("CGCarinaProcenat"))
                kola("CGTransport") = IIf(Nz(bm("CGTransport"), 0) = 0, 0, bm("CGTransport"))
                kola("cgTrosakProcenat") = IIf(Nz(bm("cgTrosakProcenat"), 0) = 0, 0, bm("cgTrosakProcenat"))
                kola("cgTrosak") = IIf(Nz(bm("cgTrosak"), 0) = 0, 0, bm("cgTrosak"))
                kola("cgKurs") = IIf(Nz(bm("cgKurs"), 0) = 0, 0, bm("cgKurs"))
                kola("cgKvanBonus") = IIf(Nz(bm("cgKvanBonus"), 0) = 0, 0, bm("cgKvanBonus"))
                kola("cgKvalBonus") = IIf(Nz(bm("cgKvalBonus"), 0) = 0, 0, bm("cgKvalBonus"))
                kola("CGVAT") = IIf(Nz(bm("CGVAT"), 0) = 0, 0, bm("CGVAT"))
                kola("CGMaloprodajnaMarzaProcenat") = IIf(Nz(bm("CGMaloprodajnaMarzaProcenat"), 0) = 0, 0, bm("CGMaloprodajnaMarzaProcenat"))
                'BiH vrednosti
                kola("bihCarinaProcenat") = IIf(Nz(bm("bihCarinaProcenat"), 0) = 0, 0, bm("bihCarinaProcenat"))
                kola("bihTransport") = IIf(Nz(bm("bihTransport"), 0) = 0, 0, bm("bihTransport"))
                kola("bihTrosakProcenat") = IIf(Nz(bm("bihTrosakProcenat"), 0) = 0, 0, bm("bihTrosakProcenat"))
                kola("bihTrosak") = IIf(Nz(bm("bihTrosak"), 0) = 0, 0, bm("bihTrosak"))
                kola("bihKurs") = IIf(Nz(bm("bihKurs"), 0) = 0, 0, bm("bihKurs"))
                kola("bihVAT") = IIf(Nz(bm("bihVAT"), 0) = 0, 0, bm("bihVAT"))
                kola("bihSNL") = IIf(Nz(bm("bihSNL"), 0) = 0, 0, bm("bihSNL"))
                kola("bihMaloprodajnaMarzaProcenat") = IIf(Nz(bm("bihMaloprodajnaMarzaProcenat"), 0) = 0, 0, bm("bihMaloprodajnaMarzaProcenat"))
                kola("bihKvanBonus") = IIf(Nz(bm("bihKvanBonus"), 0) = 0, 0, bm("bihKvanBonus"))
                kola("bihKvalBonus") = IIf(Nz(bm("bihKvalBonus"), 0) = 0, 0, bm("bihKvalBonus"))
                'Alb vrednosti
                kola("albCarinaProcenat") = IIf(Nz(bm("albCarinaProcenat"), 0) = 0, 0, bm("albCarinaProcenat"))
                kola("albTransport") = IIf(Nz(bm("albTransport"), 0) = 0, 0, bm("albTransport"))
                kola("albTrosakProcenat") = IIf(Nz(bm("albTrosakProcenat"), 0) = 0, 0, bm("albTrosakProcenat"))
                kola("albTrosak") = IIf(Nz(bm("albTrosak"), 0) = 0, 0, bm("albTrosak"))
                kola("albKurs") = IIf(Nz(bm("albKurs"), 0) = 0, 0, bm("albKurs"))
                kola("albVAT") = IIf(Nz(bm("albVAT"), 0) = 0, 0, bm("albVAT"))
                kola("albSNL") = IIf(Nz(bm("albSNL"), 0) = 0, 0, bm("albSNL"))
                kola("albMaloprodajnaMarzaProcenat") = IIf(Nz(bm("albMaloprodajnaMarzaProcenat"), 0) = 0, 0, bm("albMaloprodajnaMarzaProcenat"))
                kola("albKvanBonus") = IIf(Nz(bm("albKvanBonus"), 0) = 0, 0, bm("albKvanBonus"))
                kola("albKvalBonus") = IIf(Nz(bm("albKvalBonus"), 0) = 0, 0, bm("albKvalBonus"))
                kola("EUcarina") = bm("Eucarina")
                kola("ISP") = IIf(Nz(bm("Akcija"), 0) = 0, 0, bm("Akcija"))
                kola("PDI") = IIf(Nz(bm("PDI"), 0) = 0, 0, bm("PDI"))
                kola("NivoEmisijeID") = DLookup("NivoEmisijeID", "NivoEmisije", "Opis='" & vNivoEmisije & "'")
                kola("EkoTaksa") = IIf(Nz(bm("EkoTaksa"), 0) = 0, 0, bm("EkoTaksa"))
                kola("CarinaProcenatPrava") = IIf(Nz(bm("CarinaProcenatPrava"), 0) = 0, 0, bm("CarinaProcenatPrava"))
                kola("CarinaIznosPrava") = IIf(Nz(bm("CarinaIznosPrava"), 0) = 0, 0, bm("CarinaIznosPrava")) + IIf(Nz(bm("CarinaProcenatPrava"), 0) = 0, 0, bm("CarinaProcenatPrava") * NabavnaVrednostOpreme / 100)
                kola.Update
            End With
        
            'status vozila
            u = ""
            u = "INSERT INTO VoziloStatus ( VoziloID, RedniBroj, StatusVozilaID, DatumStatusa, Azurirao )"
            u = u & "SELECT " & IDVozilo & " AS Expr1, " & (Nz(DMax("[RedniBroj]", "VoziloStatus", "VoziloID=" & IDVozilo), 0) + 1) & " AS Expr5, 1 AS Expr2, Now() AS Expr3, " & Forms!Glavna!FReferentID & " AS Expr4;"
            DoCmd.RunSQL u
            
            'dodatna oprema u tabelu VoziloOprema
            Brojac = 0
            u = ""
            u = "INSERT INTO VoziloOprema ( Kod, OpisEngleski, Opis, Cena, VrstaOpremeID, RedniBroj, VoziloID, CenaNabavna, CenaSaCarinom, DatumOtvorio, KodAktivanPasivan )"
            u = u & "SELECT kod, naziv, prevod, formatCena([Cena]) AS Iznos, 2 AS Vrsta, RecNum([prevod],False) AS RedniBroj, " & IDVozilo & " AS Vozilo, IIf([Iznos]=0,0,Round([Iznos]*(1-" & str(bm("ENPProcenat")) & "/100))) AS Nabavna, Round((([Iznos]+[Nabavna]*(" & str(bm("carinaProcenat")) & ")/100))+0.5) AS Carina, Date() AS Datum, -1 AS kodAP FROM opremaxml WHERE (vrsta = 'F' or vrsta = 'P' or vrsta = 'L') AND (prevod NOT LIKE 'IGNORE*' OR prevod IS NULL)"
            DoCmd.RunSQL u
            
            'standardna oprema u tabelu VoziloOprema
            Brojac = 0
            u = ""
            u = "INSERT INTO VoziloOprema ( Kod, OpisEngleski, Opis, Cena, VrstaOpremeID, RedniBroj, VoziloID, CenaNabavna, CenaSaCarinom, DatumOtvorio, KodAktivanPasivan )"
            u = u & "SELECT kod, naziv, prevod, formatCena([Cena]) AS Iznos, 1 AS Vrsta, RecNum([prevod],False) AS RedniBroj, " & IDVozilo & " AS Vozilo, IIf([Iznos]=0,0,Round([Iznos]*(1-" & str(bm("ENPProcenat")) & "/100))) AS Nabavna, Round((([Iznos]+[Nabavna]*(" & str(bm("carinaProcenat")) & ")/100))+0.5) AS Carina, Date() AS Datum, -1 AS kodAP FROM opremaxml WHERE vrsta = 'S' AND (prevod NOT LIKE 'IGNORE*' OR prevod IS NULL)"
            DoCmd.RunSQL u
    
            'ostala oprema u tabelu VoziloOprema
           Brojac = 0
            u = ""
            u = "INSERT INTO VoziloOprema ( Kod, OpisEngleski, Opis, Cena, VrstaOpremeID, RedniBroj, VoziloID, CenaNabavna, CenaSaCarinom, DatumOtvorio, KodAktivanPasivan )"
            u = u & "SELECT kod, naziv, prevod, formatCena([Cena]) AS Iznos, 3 AS Vrsta, RecNum([prevod],False) AS RedniBroj, " & IDVozilo & " AS Vozilo, IIf([Iznos]=0,0,Round([Iznos]*(1-" & str(bm("ENPProcenat")) & "/100))) AS Nabavna, Round((([Iznos]+[Nabavna]*(" & str(bm("carinaProcenat")) & ")/100))+0.5) AS Carina, Date() AS Datum, -1 AS kodAP FROM opremaxml WHERE (vrsta = 'C' AND (naziv NOT LIKE 'tridion*' OR naziv IS NULL)) or vrsta = 'T' ORDER BY vrsta ASC"
            DoCmd.RunSQL u
            'izmena 20091208 da ubaci i tridion, ali kao poslednju stavku
            u = ""
            u = "INSERT INTO VoziloOprema ( Kod, OpisEngleski, Opis, Cena, VrstaOpremeID, RedniBroj, VoziloID, CenaNabavna, CenaSaCarinom, DatumOtvorio, KodAktivanPasivan )"
            u = u & "SELECT kod, naziv, prevod, formatCena([Cena]) AS Iznos, 3 AS Vrsta, RecNum([prevod],False) AS RedniBroj, " & IDVozilo & " AS Vozilo, IIf([Iznos]=0,0,Round([Iznos]*(1-" & str(bm("ENPProcenat")) & "/100))) AS Nabavna, Round((([Iznos]+[Nabavna]*(" & str(bm("carinaProcenat")) & ")/100))+0.5) AS Carina, Date() AS Datum, -1 AS kodAP FROM opremaxml WHERE vrsta = 'C' AND naziv LIKE 'tridion*'"
            DoCmd.RunSQL u
        ws.CommitTrans
        GOXMLUvoz = IDVozilo
Else
    IDVozilo = vVoziloID
    Set ws = Workspaces(0)
    ws.BeginTrans
        'Predmet poslovanja
        Set pp = CurrentDb.OpenRecordset("Select PredmetPoslovanja.* from PredmetPoslovanja where PredmetPoslovanja.PredmetPoslovanjaID =" & IDVozilo)
        With pp
            .Edit
            pp("JedinicaMereID") = 1
            pp("ZadnjaFakturnaVrednost") = bm("LLP")
            pp("UkupnaCenaSaCarinom") = Round(bm("LLP") + CenaSO + CenaDO + CenaOO)
            pp("PlanskaCena") = bm("CenaOsnovnogVozilaBezCarinePrava") + Standardna + Dodatna + Ostalo
            .Update
        End With
        
        'vozilo
        Set kola = CurrentDb.OpenRecordset("SELECT Vozilo.* FROM Vozilo WHERE Vozilo.VoziloId=" & IDVozilo)
        With kola
            kola.Edit
            kola("dlp") = bm("dlp")
            kola("dlptransport") = bm("dlptransport")
            kola("fob") = bm("fob")
            kola("ENPProcenat") = bm("ENPProcenat")
            kola("SNL") = bm("SNL")
            kola("KoeficijentOsiguranja") = bm("KoeficijentOsiguranja")
            kola("CenaTransporta") = bm("CenaTransporta")
            kola("CarinaProcenat") = bm("CarinaProcenat")
            kola("BankarskiTrosakProcenat") = bm("BankarskiTrosakProcenat")
            kola("SpediterskiTrosak") = bm("SpediterskiTrosak")
            kola("LLP") = bm("LLP")
            kola("MaloprodajnaMarzaProcenat") = bm("MaloprodajnaMarzaProcenat")
            kola("CenaOsnovnogVozilaBezCarinePrava") = bm("CenaOsnovnogVozilaBezCarinePrava")
            kola("ENP") = bm("ENP")
            kola("PosebanPopust") = bm("PosebanPopust")
            kola("PrvaNabavnaCena") = bm("PrvaNabavnaCena")
            kola("Osiguranje") = bm("Osiguranje")
            kola("CenaCIPBeograd") = bm("CenaCIPBeograd")
            kola("Carina") = bm("Carina")
            kola("BankarskiTrosak") = bm("BankarskiTrosak")
            kola("UkupnaNabavnaCena") = bm("UkupnaNabavnaCena")
            kola("UkupnoNabavnaCenaSaCarinom") = bm("UkupnaNabavnaCenaSaCarinom")
            kola("MaloprodajnaMarza") = bm("MaloprodajnaMarza")
            kola("VeleprodajnaCena") = bm("VeleprodajnaCena")
            kola("VeleProdajnaMarza") = bm("VeleProdajnaMarza")
            kola("VeleProdajnaMarzaProcenat") = bm("VeleProdajnaMarzaProcenat")
            kola("MBKSfile") = vXMLFile
            kola("CarinaOpreme") = CarinaOpreme
            kola("cenaopremesacarinom") = CenaSO + CenaDO + CenaOO
            kola("OpremaStandardnaCena") = Standardna
            kola("OpremaDodatnaCena") = Dodatna
            kola("OpremaOstalaCena") = Ostalo
            kola("OpremaStandardnaNabavnaCena") = OpremaStandardnaNabavnaCena
            kola("OpremaDodatnaNabavnaCena") = OpremaDodatnaNabavnaCena
            kola("OpremaOstalaNabavnaCena") = OpremaOstalaNabavnaCena
            kola("NabavnaVrednostOpreme") = NabavnaVrednostOpreme
            'CG vrednosti
            kola("CGSNL") = IIf(Nz(bm("CGSNL"), 0) = 0, 0, bm("CGSNL"))
            kola("CGCarina") = IIf(Nz(bm("CGCarinaProcenat"), 0) = 0, 0, bm("CGCarinaProcenat"))
            kola("CGTransport") = IIf(Nz(bm("CGTransport"), 0) = 0, 0, bm("CGTransport"))
            kola("cgTrosakProcenat") = IIf(Nz(bm("cgTrosakProcenat"), 0) = 0, 0, bm("cgTrosakProcenat"))
            kola("cgTrosak") = IIf(Nz(bm("cgTrosak"), 0) = 0, 0, bm("cgTrosak"))
            kola("cgKurs") = IIf(Nz(bm("cgKurs"), 0) = 0, 0, bm("cgKurs"))
            kola("cgKvanBonus") = IIf(Nz(bm("cgKvanBonus"), 0) = 0, 0, bm("cgKvanBonus"))
            kola("cgKvalBonus") = IIf(Nz(bm("cgKvalBonus"), 0) = 0, 0, bm("cgKvalBonus"))
            kola("CGVAT") = IIf(Nz(bm("CGVAT"), 0) = 0, 0, bm("CGVAT"))
            kola("CGMaloprodajnaMarzaProcenat") = IIf(Nz(bm("CGMaloprodajnaMarzaProcenat"), 0) = 0, 0, bm("CGMaloprodajnaMarzaProcenat"))
            'BiH vrednosti
            kola("bihCarinaProcenat") = IIf(Nz(bm("bihCarinaProcenat"), 0) = 0, 0, bm("bihCarinaProcenat"))
            kola("bihTransport") = IIf(Nz(bm("bihTransport"), 0) = 0, 0, bm("bihTransport"))
            kola("bihTrosakProcenat") = IIf(Nz(bm("bihTrosakProcenat"), 0) = 0, 0, bm("bihTrosakProcenat"))
            kola("bihTrosak") = IIf(Nz(bm("bihTrosak"), 0) = 0, 0, bm("bihTrosak"))
            kola("bihKurs") = IIf(Nz(bm("bihKurs"), 0) = 0, 0, bm("bihKurs"))
            kola("bihVAT") = IIf(Nz(bm("bihVAT"), 0) = 0, 0, bm("bihVAT"))
            kola("bihSNL") = IIf(Nz(bm("bihSNL"), 0) = 0, 0, bm("bihSNL"))
            kola("bihMaloprodajnaMarzaProcenat") = IIf(Nz(bm("bihMaloprodajnaMarzaProcenat"), 0) = 0, 0, bm("bihMaloprodajnaMarzaProcenat"))
            kola("bihKvanBonus") = IIf(Nz(bm("bihKvanBonus"), 0) = 0, 0, bm("bihKvanBonus"))
            kola("bihKvalBonus") = IIf(Nz(bm("bihKvalBonus"), 0) = 0, 0, bm("bihKvalBonus"))
            'Alb vrednosti
            kola("albCarinaProcenat") = IIf(Nz(bm("albCarinaProcenat"), 0) = 0, 0, bm("albCarinaProcenat"))
            kola("albTransport") = IIf(Nz(bm("albTransport"), 0) = 0, 0, bm("albTransport"))
            kola("albTrosakProcenat") = IIf(Nz(bm("albTrosakProcenat"), 0) = 0, 0, bm("albTrosakProcenat"))
            kola("albTrosak") = IIf(Nz(bm("albTrosak"), 0) = 0, 0, bm("albTrosak"))
            kola("albKurs") = IIf(Nz(bm("albKurs"), 0) = 0, 0, bm("albKurs"))
            kola("albVAT") = IIf(Nz(bm("albVAT"), 0) = 0, 0, bm("albVAT"))
            kola("albSNL") = IIf(Nz(bm("albSNL"), 0) = 0, 0, bm("albSNL"))
            kola("albMaloprodajnaMarzaProcenat") = IIf(Nz(bm("albMaloprodajnaMarzaProcenat"), 0) = 0, 0, bm("albMaloprodajnaMarzaProcenat"))
            kola("albKvanBonus") = IIf(Nz(bm("albKvanBonus"), 0) = 0, 0, bm("albKvanBonus"))
            kola("albKvalBonus") = IIf(Nz(bm("albKvalBonus"), 0) = 0, 0, bm("albKvalBonus"))
            kola("EUcarina") = bm("Eucarina")
            kola("ISP") = IIf(Nz(bm("Akcija"), 0) = 0, 0, bm("Akcija"))
            kola("PDI") = IIf(Nz(bm("PDI"), 0) = 0, 0, bm("PDI"))
            kola("NivoEmisijeID") = DLookup("NivoEmisijeID", "NivoEmisije", "Opis='" & vNivoEmisije & "'")
            kola("EkoTaksa") = IIf(Nz(bm("EkoTaksa"), 0) = 0, 0, bm("EkoTaksa"))
            kola("CarinaProcenatPrava") = IIf(Nz(bm("CarinaProcenatPrava"), 0) = 0, 0, bm("CarinaProcenatPrava"))
            kola("CarinaIznosPrava") = IIf(Nz(bm("CarinaIznosPrava"), 0) = 0, 0, bm("CarinaIznosPrava")) + IIf(Nz(bm("CarinaProcenatPrava"), 0) = 0, 0, bm("CarinaProcenatPrava") * NabavnaVrednostOpreme / 100)
            kola.Update
        End With
    
        'dodatna - VoziloOprema
        Brojac = 0
        u = ""
        u = "INSERT INTO VoziloOprema ( Kod, OpisEngleski, Opis, Cena, VrstaOpremeID, RedniBroj, VoziloID, CenaNabavna, CenaSaCarinom, DatumOtvorio, KodAktivanPasivan )"
        u = u & "SELECT kod, naziv, prevod, formatCena([Cena]) AS Iznos, 2 AS Vrsta, RecNum([prevod],False) AS RedniBroj, " & IDVozilo & " AS Vozilo, IIf([Iznos]=0,0,Round([Iznos]*(1-" & str(bm("ENPProcenat")) & "/100))) AS Nabavna, Round((([Iznos]+[Nabavna]*(" & str(bm("carinaProcenat")) & ")/100))+0.5) AS Carina, Date() AS Datum, -1 AS kodAP FROM opremaxml WHERE (vrsta = 'F' or vrsta = 'P' or vrsta = 'L') AND (prevod NOT LIKE 'IGNORE*' OR prevod IS NULL)"
        DoCmd.RunSQL u
        
        'standardna - VoziloOprema
        Brojac = 0
        u = ""
        u = "INSERT INTO VoziloOprema ( Kod, OpisEngleski, Opis, Cena, VrstaOpremeID, RedniBroj, VoziloID, CenaNabavna, CenaSaCarinom, DatumOtvorio, KodAktivanPasivan )"
        u = u & "SELECT kod, naziv, prevod, formatCena([Cena]) AS Iznos, 1 AS Vrsta, RecNum([prevod],False) AS RedniBroj, " & IDVozilo & " AS Vozilo, IIf([Iznos]=0,0,Round([Iznos]*(1-" & str(bm("ENPProcenat")) & "/100))) AS Nabavna, Round((([Iznos]+[Nabavna]*(" & str(bm("carinaProcenat")) & ")/100))+0.5) AS Carina, Date() AS Datum, -1 AS kodAP FROM opremaxml WHERE vrsta = 'S' AND (prevod NOT LIKE 'IGNORE*' OR prevod IS NULL)"
        DoCmd.RunSQL u

        'ostala - VoziloOprema
        Brojac = 0
        u = ""
        u = "INSERT INTO VoziloOprema ( Kod, OpisEngleski, Opis, Cena, VrstaOpremeID, RedniBroj, VoziloID, CenaNabavna, CenaSaCarinom, DatumOtvorio, KodAktivanPasivan )"
        u = u & "SELECT kod, naziv, prevod, formatCena([Cena]) AS Iznos, 3 AS Vrsta, RecNum([prevod],False) AS RedniBroj, " & IDVozilo & " AS Vozilo, IIf([Iznos]=0,0,Round([Iznos]*(1-" & str(bm("ENPProcenat")) & "/100))) AS Nabavna, Round((([Iznos]+[Nabavna]*(" & str(bm("carinaProcenat")) & ")/100))+0.5) AS Carina, Date() AS Datum, -1 AS kodAP FROM opremaxml WHERE (vrsta = 'C' AND (naziv NOT LIKE 'tridion*' OR naziv IS NULL)) or vrsta = 'T' ORDER BY vrsta ASC"
        DoCmd.RunSQL u
        'Smart vozilo - tridion, ali kao poslednju stavku
        u = ""
        u = "INSERT INTO VoziloOprema ( Kod, OpisEngleski, Opis, Cena, VrstaOpremeID, RedniBroj, VoziloID, CenaNabavna, CenaSaCarinom, DatumOtvorio, KodAktivanPasivan )"
        u = u & "SELECT kod, naziv, prevod, formatCena([Cena]) AS Iznos, 3 AS Vrsta, RecNum([prevod],False) AS RedniBroj, " & IDVozilo & " AS Vozilo, IIf([Iznos]=0,0,Round([Iznos]*(1-" & str(bm("ENPProcenat")) & "/100))) AS Nabavna, Round((([Iznos]+[Nabavna]*(" & str(bm("carinaProcenat")) & ")/100))+0.5) AS Carina, Date() AS Datum, -1 AS kodAP FROM opremaxml WHERE vrsta = 'C' AND naziv LIKE 'tridion*'"
        DoCmd.RunSQL u
        
        Set rst = CurrentDb.OpenRecordset("TraceLog")
        With rst
            .AddNew
            .Fields("InstalacijaID") = DLookup("[InstalacijaID]", "System")
            .Fields("No") = Nz(DMax("[No]", "TraceLog"), 0) + 1
            .Fields("DateTime") = Now()
            
            If vKonfigurator = "GOT" Then
                .Fields("FormName") = "GoTransfer"
                .Fields("UserName") = "GO Interface"
                .Fields("After") = "Kreirana oprema"
                .Fields("AttributeName") = "Dodata oprema"
            Else
                .Fields("FormName") = "PonudaGO"
                .Fields("UserName") = DLookup("[Prezime]", "FizickoLice", "FizickoLiceID=" & DLookup("[ReferentID]", "System")) & " " & DLookup("[Ime]", "FizickoLice", "FizickoLiceID=" & DLookup("[ReferentID]", "System"))
                .Fields("After") = "Kreirana oprema"
                .Fields("AttributeName") = "Dodata oprema"
            End If
            
            .Fields("RecordID") = IDVozilo
            .Update
        End With
    ws.CommitTrans
    GOXMLUvoz = IDVozilo
End If

Exitfunction:
Set ws = Nothing
Set pp = Nothing
Set kola = Nothing
Set bm = Nothing
Set doq = Nothing
Set soq = Nothing
Set ooq = Nothing
Exit Function

' u slucaju greske da sve vrati unazad
ErrorHandler:
ws.Rollback
MsgBox Err.Description & Chr(13) & Chr(10) & Chr(13) & Chr(10) & "Pokusajte ponovo uvoz", vbOKOnly, "GRESKA!"
Resume Exitfunction
End Function
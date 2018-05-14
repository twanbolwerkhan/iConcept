/*
Veilingsite Eenmaal Andermaal
Auteurs: Michael Kalil 590395, Twan Bolwerk 598576, Ivan Miladinovic 599294, Janno Onink 602808, Suzanne Bogaard 603439, Auke Onvlee 604640
Datum: 26-04-2018

Sommige velden hebben we veranderd en Britse internationaal standaarden op gebruikt: 
http://webarchive.nationalarchives.gov.uk/+/http://www.cabinetoffice.gov.uk/media/254290/GDS%20Catalogue%20Vol%202.pdf

*/

use iConcept 

DROP TABLE [Bod] 

DROP TABLE [VerificatieVerkoper]

DROP TABLE [Bestand] 

DROP TABLE [Voorwerp_in_Rubriek] 

DROP TABLE [Feedback] 

DROP TABLE [Voorwerp] 

DROP TABLE [Gebruikerstelefoon] 

DROP TABLE [Rubriek] 

DROP TABLE [Verkoper] 

DROP TABLE [Verificatiecode] 

DROP TABLE [Gebruiker] 

DROP TABLE [Vraag] 

DROP TABLE [Landen]



  
create table dbo.Vraag  
(  
vraagnummer int identity not null,  
vraag varchar(255) not null,  /* een vraag kan lang en uitgebreid zijn maar ook variabel. */
constraint pk_vraagnummer primary key (vraagnummer)
)  

create table dbo.Gebruiker  
(  
gebruikersnaam varchar(25) not null,  /* https://stackoverflow.com/questions/7035974/what-is-the-preferred-length-of-a-username-and-password */
voornaam varchar(35) not null,  /* volgens britse internationale standaard */
achternaam varchar(35) not null,  /* zie hierboven */
adresregel1 varchar(35) not null,  /* zie hierboven*/
adresregel2 varchar(35) null,   
postcode varchar(10) not null,  /* https://en.wikipedia.org/wiki/Postal_code */
plaatsnaam varchar(85) not null, /* https://www.alletop10lijstjes.nl/10-langste-plaatsnamen-in-de-wereld/ */  
land varchar(48) not null,  /* http://www.funtrivia.com/askft/Question33835.html */
geboortedatum date not null, 
email varchar(100) not null,  /* Kan niet met dezelfde email registeren, MOET AANGEPAST NOG WORDEN 25-04-2018 */
wachtwoord varchar(50) not null, /* Vergroot voor de SHA256 HASH */
vraagnummer int not null, 
antwoordtekst varchar(25) not null, /* de antwoord kan niet altijd 6 characters zijn, is variabel en soms langer, ligt aan de vraag */
verkoper bit not null, /* kan beste bit zijn voor true(wel) en false(niet) */
geactiveerd bit not null, /* toevoeging we kunnen zien of accounts geactiveerd zijn */
CONSTRAINT pk_gebruikersnaam_email PRIMARY KEY (gebruikersnaam),  
CONSTRAINT fk_Gebruiker_vraagnummer FOREIGN KEY (vraagnummer) 
REFERENCES Vraag (vraagnummer),  
constraint ck_email check(email like '%___@___%.__%')  
) 

/* toevoeging tabel voor verificatie code */
create table dbo.Verificatiecode  
( 
gebruikersnaam varchar(25) not null, 
begintijd bigint not null, 
code char(6) not null, 
CONSTRAINT pk_verificatiecode PRIMARY KEY (gebruikersnaam, begintijd, code), 
CONSTRAINT fk_verificatiecode_gebruikersnaam FOREIGN KEY (gebruikersnaam)  
REFERENCES Gebruiker(gebruikersnaam) 

)  

  

create table dbo.Verkoper  
( 
gebruikersnaam varchar(25) not null,  /* https://stackoverflow.com/questions/7035974/what-is-the-preferred-length-of-a-username-and-password */
banknaam varchar(75),   /* https://forums.collectors.com/discussion/960991/whats-the-longest-national-bank-name */
rekeningnummer varchar(34),  /* https://en.wikipedia.org/wiki/International_Bank_Account_Number */
controleoptienaam varchar(11) not null, /* is of creditcard of post */
creditcardnummer varchar(19), /* https://en.wikipedia.org/wiki/Payment_card_number */
CONSTRAINT pk_gebruikersnaam PRIMARY KEY (gebruikersnaam),  
CONSTRAINT fk_VerkoperGebruiker_GebrGebruikersnaam FOREIGN KEY (gebruikersnaam)  
REFERENCES Gebruiker (gebruikersnaam) ,
CONSTRAINT CHK_domain_CreditOfPost CHECK (controleoptienaam IN ('Creditcard','Post'))
)  

  

  

  

create table dbo.Voorwerp  
(  
voorwerpnummer bigint identity(1,1) not null,  /* toelichting: zodat we veel voorwerpnummers kunnen opslaan */
titel varchar(50) not null,  /* titel is altijd variabel */
beschrijving varchar(255) not null,  /* Beschrijving is variabel en kan van alles bevatten zoals specificatie, historie */
startprijs numeric(9,2) not null,  
betalingswijze varchar(25) not null,  
betalingsinstructie char(255),  /* instructie om het geld te verzenden */
plaatsnaam varchar(50) not null,  /* locatie van het voorwerp */
land varchar(48) not null,   /* http://www.funtrivia.com/askft/Question33835.html */
looptijd int not null,   
looptijdbegindag date not null,  
looptijdtijdstip time not null,  /* looptijd einde tijdstip verwijdert omdat AF2 hetzelfde wil en naamgeving verandert naar tijdstip */
verzendkosten numeric(5,2),  
verzendinstructies varchar(255),  
verkoper varchar(25) not null,  /* afgestemd op gebruikersnaam */
koper varchar(25),   /* afgestemd op gebruikersnaam */
looptijdeindedag date not null, 
veilinggesloten bit not null,  /* Bit als true/false = wel/niet */
verkoopprijs numeric(9,2),  
CONSTRAINT pk_voorwerpnummer PRIMARY KEY (voorwerpnummer),  
CONSTRAINT fk_verkoper_gebruiker FOREIGN KEY (verkoper)  
REFERENCES Verkoper (gebruikersnaam),  
CONSTRAINT fk_koper_gebruiker FOREIGN KEY (koper)  
REFERENCES Gebruiker (gebruikersnaam)  
)  


  

create table dbo.Bestand  
(
filenaam varchar(50) not null,  /* een variabele filenaam want niet elke filenaam is even groot. Verlengd voor combinaties */
voorwerpnummer bigint not null,  /* bigint zodat er zoveel combinaties zijn */
CONSTRAINT pk_filenaam PRIMARY KEY (filenaam),  
CONSTRAINT fk_Bestand_voorwerpnummer FOREIGN KEY (voorwerpnummer)  
REFERENCES Voorwerp (voorwerpnummer)  
)  

  
  

create table dbo.Bod  
(  
voorwerpnummer bigint not null,  /* zie hierboven */
bodbedrag numeric(9,2) not null,  /* numeric voor komma's en meer grotere bedragen */
gebruikersnaam varchar(25) not null,  
boddag date not null,  /* date is beter om dag op te slaan dan een char, werkt ook beter met applicaties dan een int/char*/
bodtijdstip DATETIME not null,  /* normaal date slaat de tijd niet op, daarvoor gebruiken we datetime */
CONSTRAINT pk_voorwerp_bodbedrag PRIMARY KEY (voorwerpnummer, bodbedrag),  
CONSTRAINT fk_BodVoorwerp_voorwerpnr FOREIGN KEY (voorwerpnummer)  
REFERENCES Voorwerp (voorwerpnummer),  
CONSTRAINT fk_Bod_gebruikersnaam FOREIGN KEY (gebruikersnaam)  
REFERENCES Gebruiker (gebruikersnaam)  
)  


create table dbo.Feedback  
(  
voorwerpnummer bigint not null,  
soortgebruiker varchar(8) not null,  /* variabel want het is of 5 characters of 8 */
feedbacksoort char(8) not null,  
datum date not null,  
tijdaanduiding datetime not null,   
commentaar varchar(255),  
CONSTRAINT pk_voorwerpnummer_SoortGebruiker PRIMARY KEY (voorwerpnummer, soortgebruiker),  
CONSTRAINT fk_Feedback_ref_voorwerpnummer FOREIGN KEY (voorwerpnummer)  
REFERENCES Voorwerp (voorwerpnummer),  
CONSTRAINT ck_feedbacksoortnaam CHECK (feedbacksoort IN ('positief', 'negatief', 'neutraal')),  
CONSTRAINT ck_KoperVerkoper CHECK (soortgebruiker IN ('koper', 'verkoper'))  
)  

  

create table dbo.Gebruikerstelefoon  
(  
volgnummer int identity(1,1) not null,  
gebruikersnaam varchar(25) not null,   
telefoonnummer varchar(15) not null,  /* https://en.wikipedia.org/wiki/E.164 */
CONSTRAINT pk_volgnr_gebruikersnaam PRIMARY KEY (volgnummer, gebruikersnaam),  
CONSTRAINT fk_GebrTelefoon_GebrGebruikersnaam FOREIGN KEY (gebruikersnaam)  
REFERENCES Gebruiker (gebruikersnaam)  
)  

  
  

create table dbo.Rubriek  
(  
rubrieknummer int not null,  
rubrieknaam varchar(50) not null, /* De rubrieknaam kan langer zijn dan 24 en is ook variabel daarom Varchar 50 */ 
rubrieknummerOuder int,  
volgnummer int not null,  
CONSTRAINT pk_rubrieknummer PRIMARY KEY (rubrieknummer),  
CONSTRAINT fk_RubOudNr_Rub_Nr FOREIGN KEY (rubrieknummerOuder)  
REFERENCES Rubriek (rubrieknummer)  
)  


  

create table dbo.Voorwerp_in_Rubriek  
(  
voorwerpnummer bigint not null,  
rubrieknummer int not null,  
CONSTRAINT pk_voorwerpnummer_rubrieknummer PRIMARY KEY (voorwerpnummer, rubrieknummer),  
CONSTRAINT fk_RubVoorwerp_VoorwVoorwerpnummer FOREIGN KEY (voorwerpnummer)  
REFERENCES Voorwerp (voorwerpnummer),
CONSTRAINT fk_RubriekRubOpLaagsteNiv_RubriekRubrieknummer FOREIGN KEY (rubrieknummer)  
REFERENCES Rubriek (rubrieknummer)
)  


create table dbo.Landen
(
landnaam varchar(49) not null /* http://www.funtrivia.com/askft/Question33835.html */
CONSTRAINT pk_landen PRIMARY KEY (landnaam)
)


create table dbo.VerificatieVerkoper
(
gebruikersnaam varchar(25) not null,
code char(6) not null,
CONSTRAINT pk_verificatieverkoper PRIMARY KEY (gebruikersnaam, code), 
CONSTRAINT fk_verificatieverkoper_gebruikersnaam FOREIGN KEY (gebruikersnaam)  
REFERENCES Gebruiker(gebruikersnaam) 
)

 
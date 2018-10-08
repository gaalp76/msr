/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: HU (Hungarian; Magyar)
 */
$.extend( $.validator.messages, {
	empty_firstname: "A vezetéknév mező nincs kitöltve!",
	empty_lastname: "A keresztnév mező nincs kitöltve!",
	empty_email: "Az emailcím mező nincs kitöltve!",
	empty_username: "A felhasználónév mező minimum 5 karakter hosszú!",
	empty_shipping_city: "A szállítási cím (település) mező nincs kitöltve!",
	empty_shipping_address: "A szállítási cím (utca, hsz.) mező nincs kitöltve!",
	empty_shipping_zip: "Az szállítási cím irányítószám mező nincs kitöltve!",
	empty_phone: "A mobil telefon mező nincs kitöltve!",
	empty_bank_account_number: "A bankszámlaszám mező nincs kitöltve!",
	empty_tax_number: "Az adószám mező nincs kitöltve!",
	empty_order: "Az Ön kosara üres!",
	empty_news_editor: "A hír tartalma üres!",
	empty_news_meta_data: "A hír metaadatai hiányosak!",
	empty_username: "Kérjük, válasszon felhasználót!",
	empty_login_username: "Kérjük, adja meg a felhasználónevet!",
	empty_login_password: "Kérjük, adja meg a jelszót!",
	empty_news_search: "A keresés nem eredményezett találatot!",
	empty_news_assign: "Nincs hír kijelölve!",
	empty_folder_name: "Hiányzó név.",
	empty_uploadmanager_folders: "A mappa/album tartalma üres.",
	empty_document_handler: "Nincsenek feltöltött dokumentumok.",
        empty_selected_in_list: "Kérjük, jelöljön ki egy lista elemet!",
	
	failed_firstname: "A vezetéknév mező betűket, és szóközöket tartalmazhat!",
	failed_lastname: "A keresztnév mező betűket, és szóközöket tartalmazhat!",
	failed_username: "A felhasználónév mező betűket, számokat és szóközöket tartalmazhat!",
	failed_phone: "A mobil telefon mező formátuma hibás (00/000-0000)!",
	failed_shipping_zip: "Az irányítószám mező formátuma hibás (0000)!",
	failed_mail: "Levelezőrendszer hiba! Kérjük, forduljon a rendszergazdához!",
	failed_emial: "Az email cím formátuma érvénytelen!", 
	failed_database: "Adatbázis hiba! Kérjük, forduljon a rendszergazdához!",
	failed_login: "Hibás felhasználónév, vagy jelszó!",
	failed_password: "A jelszó mező legalább 5 karakter hosszú legyen!",
	failed_bank_account_number: "A bankszámla formátuma hibás! (00000000-00000000-[00000000])",
	failed_company_zip: "A cég irányítószám mező formátuma hibás! (0000)",
	failed_mailing_zip: "A levelezési cím irányítószám mező formátuma hibás! (0000)",
	failed_contact_phone: "A kapcsolattartó mobil telefon mező formátuma hibás (00/000-0000)!",
	failed_contact_finance_phone: "A pénzügyi kapcsolattartó mobil telefon mező formátuma hibás (00/000-0000)!",
	failed_fields:"Hiányosan kitöltött mezők találhatók az űrlapon. Hibás mezők száma: ",
	failed_user_validation:"A regisztrációs adatok érvényesítése sikertelen volt. Kérjük, próbálja meg később!",
	failed_corporation_register_number: "A cégjegyzékszám mező formátuma hibás! (00-00-000000)",
	failed_tax_number: "Az adószám mező formátuma hibás! (00000000-0-00)",
	failed_forgotpassword: "Az email cím nem szerepel adatbázisunkban!",
	failed_privilege: "Az Ön regisztrációját még nem hitelesítettük! Kérjük türelmét! Köszönjük.",
	failed_adduser: "Hiba történt a felhasználó adatainak mentése közben. Kérjük próbálja később újra.",
	failed_user_authority:"Ön nem jogosult az oldal megtekintésére.",
	failed_create_start_folder: "Kezdőkönyvtár-struktúra létrehozása sikertelnen.",
	failed_create_folder: "A mappa/album létrehozás sikertelen.",
    failed_create_folder_hu:"A megadott név már foglalt (magyar).",
    failed_create_folder_en:"A megadott név már foglalt (angol).",
    failed_create_folder_de:"A megadott név már foglalt (német).",
	failed_upload_file_ext: "Nem megengedett kiterjesztés.",
	failed_folder_create: "Könyvtár létrehozása sikertelen! Kérjük, forduljon a rendszergazdához!",
	failed_invalid_folder_name: "Hibás mappa/album megnevezés!",
	failed_delete_files: "Az albumban/könyvtárban lévő fájlok törlése sikertelen!",
	failed_delete_folder: "A album/könyvtár törlése sikertelen!",

	exist_email: "Az email cím már szerepel az adatbázisban!",
	exist_username: "A felhasználónév  már szerepel az adatbázisban!",
	
	success_signup: "Az új felhasználó adatainak mentése megtörtént.",	
	success_login: "Köszöntjük a Szombathelyi Műszaki Szakkézpési Centrum Admin felületén!",
	success_logout: "Viszontlátásra!",
	success_user_validation: "Köszönjük regisztrációját! A validálási folyamat zárásaként email értesítés után használhatja fiókját.",
	success_forgotpassword: "Az új jelszót elküldtük email címére.",
	success_data_modify: "Az adatok módosítása megtörtént.",
	success_order: "Köszönjük rendelését!",
	success_user_add: "A felhasználó rögzítése megtörtént.",
	success_user_delete: "A felhasználó törlése megtörtént.",
	success_user_authority: "A jogosultság rögzítése megtörtént.",
	success_user_delete_authority: "A jogosultság törlése megtörtént.",
	success_user_update: "A felhasználó adatainak módosítása megtörtént.",
	success_news_add: "Hír rögzítése megtörtént",
	success_news_modify: "Hír módosítása megtörtént",
	success_news_modify: "Hír módosítása megtörtént",
	success_news_delete: "Hír törlése megtörtént",
    success_album_insert: "Az album rögzítése megtörtént.",
	success_album_delete: "Az album(ok) törlése megtörtént.",
	success_folder_modify: "Az album módosítása megtörtént.",
	success_pict_upload: "A képek feltöltése megtörtént.",
	success_pict_delete: "A képek törlése megtörtént.",
	success_aboutus_save: "A versenyszabályzat rögzítése megtörtént.",
	success_folder_insert : "A mentés megtörtént.",
	success_pict_delete : "A kép(ek) törlése megtörtént.",
	success_subtitle_modify: "A fájl átnevezése megtörtént.",
	success_delete: "A törlés megtörtént.",
	success_save: "A mentés megtörtént.",
	success_document_attachment: "Dokumentumok csatolása megtörtént.",
	success_document_attachment_delete: "Dokumentum csatolások törlése megtörtént.",
	success_other_save: "Másik időpontra való átállás megtörtént.",

	required: "Kötelező megadni.",
	maxlength: $.validator.format( "Legfeljebb {0} karakter hosszú legyen." ),
	minlength: $.validator.format( "Legalább {0} karakter hosszú legyen." ),
	rangelength: $.validator.format( "Legalább {0} és legfeljebb {1} karakter hosszú legyen." ),
	email: "Érvényes e-mail címnek kell lennie.",
	url: "Érvényes URL-nek kell lennie.",
	date: "Dátumnak kell lennie.",
	number: "Számnak kell lennie.",
	digits: "Csak számjegyek lehetnek.",
	equalTo: "Meg kell egyeznie a két jelszónak.",
	range: $.validator.format( "{0} és {1} közé kell esnie." ),
	max: $.validator.format( "Nem lehet nagyobb, mint {0}." ),
	min: $.validator.format( "Nem lehet kisebb, mint {0}." ),
	creditcard: "Érvényes hitelkártyaszámnak kell lennie.",
	remote: "Kérem javítsa ki ezt a mezőt.",
	dateISO: "Kérem írjon be egy érvényes dátumot (ISO).",
	step: $.validator.format( "A {0} egyik többszörösét adja meg." )
} );


			  
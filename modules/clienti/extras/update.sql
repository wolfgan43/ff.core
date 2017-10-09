 INSERT INTO cm_mod_clienti_main (ragsoc,email,telefono,cellulare,piva,cf,indirizzo,cap,citta,provincia,nazione,fax)

SELECT ragsoc, email, telefono, cellulare, piva, codf, indirizzo, cap, citta, provincia, nazione, fax


INSERT INTO cm_mod_clienti_contatti (ID_clienti, nome, cognome, telefono, cellulare, email, nascita, fax) SELECT cm_mod_clienti_main.ID, clienti_contatti.nome, clienti_contatti.cognome,clienti_contatti.telefono,clienti_contatti.cellulare,clienti_contatti.email,clienti_contatti.nascita,clienti_contatti.fax FROM clienti_contatti INNER JOIN cm_mod_clienti_main ON clienti_contatti.ID_clienti = cm_mod_clienti_main.old_ID
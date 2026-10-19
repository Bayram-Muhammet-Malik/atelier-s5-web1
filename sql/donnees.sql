-- Données de test NRV.net
-- À importer APRÈS SAE_tables.sql
-- Mot de passe de tous les comptes : nrv2026

--  Lieux
INSERT INTO lieu (id, nom, adresse, nb_places) VALUES
(1, 'L''Autre Canal', '45 boulevard d''Austrasie, 54000 Nancy', 1200),
(2, 'Salle Poirel', '3 rue Victor Poirel, 54000 Nancy', 700),
(3, 'La Péniche', 'Quai de la Bataille, 54000 Nancy', 20);

--  Styles
INSERT INTO style (id, libelle) VALUES
(1, 'Classic rock'),
(2, 'Blues rock'),
(3, 'Métal'),
(4, 'Reggae'),
(5, 'Chanson'),
(6, 'Rock alternatif');

--  Soirées
INSERT INTO soiree (id, nom, thematique, date_soiree, heure_debut, places_restantes, id_lieu) VALUES
(1, 'Ouverture du festival', 'Soirée classic rock', '2027-06-11', '20:00:00', 1196, 1),
(2, 'Nuit du blues', 'Soirée blues', '2027-06-12', '20:30:00', 697, 2),
(3, 'Métal Fury', 'Soirée métal', '2027-06-13', '19:30:00', 1200, 1),
(4, 'Reggae Vibes', 'Soirée reggae', '2027-06-15', '20:00:00', 699, 2),
(5, 'Chansons au fil de l''eau', 'Soirée chanson', '2027-06-16', '20:30:00', 0, 3),
(6, 'Alternative Night', 'Soirée rock alternatif', '2027-06-17', '20:00:00', 1200, 1),
(7, 'Blues & Rock Session', 'Soirée blues rock', '2027-06-18', '20:30:00', 3, 3),
(8, 'Métal Fury II', 'Soirée métal', '2027-06-19', '19:30:00', 1200, 1),
(9, 'Mots et Mélodies', 'Soirée chanson française', '2027-06-22', '20:00:00', 700, 2),
(10, 'Clôture : Rock en fête', 'Soirée rock', '2027-06-24', '19:00:00', 1200, 1);

--  Spectacles
INSERT INTO spectacle (id, titre, description, url_video, horaire, id_soiree, id_style) VALUES
(1, 'Les Vieilles Guitares', 'Les grands standards du rock des années 70 rejoués avec une énergie intacte.', 'https://videos.nrv.net/spectacle-1.mp4', '20:00:00', 1, 1),
(2, 'Highway 54', 'Un quatuor nancéien qui fait revivre l''esprit des grandes tournées américaines.', 'https://videos.nrv.net/spectacle-2.mp4', '21:30:00', 1, 1),
(3, 'Thunder Road Revival', 'Riffs saturés et harmonica : le pont entre le blues et le rock.', 'https://videos.nrv.net/spectacle-3.mp4', '23:00:00', 1, 2),
(4, 'Delta Moselle', 'Un duo guitare-voix qui transpose le blues du Mississippi sur les bords de la Moselle.', 'https://videos.nrv.net/spectacle-4.mp4', '20:30:00', 2, 2),
(5, 'Blue Stanislas', 'Blues électrique, cuivres et longues improvisations.', 'https://videos.nrv.net/spectacle-5.mp4', '22:00:00', 2, 2),
(6, 'Iron Lorraine', 'Heavy métal à l''ancienne, solos jumeaux et refrains à reprendre en chœur.', 'https://videos.nrv.net/spectacle-6.mp4', '19:30:00', 3, 3),
(7, 'Abyssal Forge', 'Un métal sombre et massif, porté par une double grosse caisse implacable.', 'https://videos.nrv.net/spectacle-7.mp4', '21:00:00', 3, 3),
(8, 'Nordic Storm', 'Du métal mélodique aux accents folkloriques venus du nord.', 'https://videos.nrv.net/spectacle-8.mp4', '22:30:00', 3, 3),
(9, 'Roots of Nancy', 'Reggae roots, basse ronde et messages positifs.', 'https://videos.nrv.net/spectacle-9.mp4', '20:00:00', 4, 4),
(10, 'Island Sound System', 'Un sound system complet pour danser jusqu''au bout de la nuit.', 'https://videos.nrv.net/spectacle-10.mp4', '21:30:00', 4, 4),
(11, 'Lucie Marchal', 'Chansons douces-amères accompagnées au piano, dans l''intimité de la péniche.', 'https://videos.nrv.net/spectacle-11.mp4', '20:30:00', 5, 5),
(12, 'Les Mots Bleus', 'Un trio acoustique qui revisite la chanson française avec humour.', 'https://videos.nrv.net/spectacle-12.mp4', '22:00:00', 5, 5),
(13, 'Static Bloom', 'Guitares noisy et mélodies pop pour un rock alternatif lumineux.', 'https://videos.nrv.net/spectacle-13.mp4', '20:00:00', 6, 6),
(14, 'Echo Parade', 'Un rock indé nerveux, entre post-punk et envolées shoegaze.', 'https://videos.nrv.net/spectacle-14.mp4', '21:30:00', 6, 6),
(15, 'Paper Satellites', 'Le groupe révélation de la scène lorraine, en première partie.', 'https://videos.nrv.net/spectacle-15.mp4', '23:00:00', 6, 6),
(16, 'Mud & Whiskey', 'Slide guitar et voix rocailleuse pour un blues rock poisseux.', 'https://videos.nrv.net/spectacle-16.mp4', '20:30:00', 7, 2),
(17, 'Route 66 Express', 'Du rock''n''roll pur jus, sans fioritures.', 'https://videos.nrv.net/spectacle-17.mp4', '22:00:00', 7, 1),
(18, 'Black Mirabelle', 'Le métal le plus lorrain du festival, puissant et sans concession.', 'https://videos.nrv.net/spectacle-18.mp4', '19:30:00', 8, 3),
(19, 'Steel Cathedral', 'Un métal symphonique grandiose avec chœurs et orchestrations.', 'https://videos.nrv.net/spectacle-19.mp4', '21:00:00', 8, 3),
(20, 'Camille Fontaine', 'Une auteure-compositrice aux textes ciselés et à la voix envoûtante.', 'https://videos.nrv.net/spectacle-20.mp4', '20:00:00', 9, 5),
(21, 'Le Petit Orchestre', 'Chanson festive façon guinguette, accordéon et contrebasse.', 'https://videos.nrv.net/spectacle-21.mp4', '21:30:00', 9, 5),
(22, 'Hugo & Léa', 'Un duo complice qui chante l''amour, les trains et les villes de l''Est.', 'https://videos.nrv.net/spectacle-22.mp4', '23:00:00', 9, 5),
(23, 'Highway 54 & Friends', 'Le concert de clôture : Highway 54 invite Lucie Marchal pour un final unique.', 'https://videos.nrv.net/spectacle-23.mp4', '19:00:00', 10, 1),
(24, 'Abyssal Forge', 'Retour d''Abyssal Forge pour un set exclusif de clôture.', 'https://videos.nrv.net/spectacle-24.mp4', '20:30:00', 10, 3),
(25, 'Static Bloom', 'Static Bloom referme le festival avec ses morceaux les plus fédérateurs.', 'https://videos.nrv.net/spectacle-25.mp4', '22:00:00', 10, 6);

--  Artistes
INSERT INTO artiste (id, nom) VALUES
(1, 'Les Vieilles Guitares'),
(2, 'Highway 54'),
(3, 'Thunder Road Revival'),
(4, 'Paul Kieffer'),
(5, 'Anna Weber'),
(6, 'Blue Stanislas'),
(7, 'Iron Lorraine'),
(8, 'Abyssal Forge'),
(9, 'Nordic Storm'),
(10, 'Roots of Nancy'),
(11, 'Island Sound System'),
(12, 'Lucie Marchal'),
(13, 'Les Mots Bleus'),
(14, 'Static Bloom'),
(15, 'Echo Parade'),
(16, 'Paper Satellites'),
(17, 'Mud & Whiskey'),
(18, 'Route 66 Express'),
(19, 'Black Mirabelle'),
(20, 'Steel Cathedral'),
(21, 'Camille Fontaine'),
(22, 'Le Petit Orchestre'),
(23, 'Hugo Lambert'),
(24, 'Léa Rousseau');

--  Spectacles / artistes
INSERT INTO spectacle_artiste (id_spectacle, id_artiste) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(4, 5),
(5, 6),
(6, 7),
(7, 8),
(8, 9),
(9, 10),
(10, 11),
(11, 12),
(12, 13),
(13, 14),
(14, 15),
(15, 16),
(16, 17),
(17, 18),
(18, 19),
(19, 20),
(20, 21),
(21, 22),
(22, 23),
(22, 24),
(23, 2),
(23, 12),
(24, 8),
(25, 14);

--  Images
INSERT INTO image (id, url, id_spectacle) VALUES
(1, 'img/spectacles/spectacle-1-1.jpg', 1),
(2, 'img/spectacles/spectacle-2-1.jpg', 2),
(3, 'img/spectacles/spectacle-3-1.jpg', 3),
(4, 'img/spectacles/spectacle-3-2.jpg', 3),
(5, 'img/spectacles/spectacle-4-1.jpg', 4),
(6, 'img/spectacles/spectacle-5-1.jpg', 5),
(7, 'img/spectacles/spectacle-5-2.jpg', 5),
(8, 'img/spectacles/spectacle-5-3.jpg', 5),
(9, 'img/spectacles/spectacle-6-1.jpg', 6),
(10, 'img/spectacles/spectacle-6-2.jpg', 6),
(11, 'img/spectacles/spectacle-7-1.jpg', 7),
(12, 'img/spectacles/spectacle-8-1.jpg', 8),
(13, 'img/spectacles/spectacle-9-1.jpg', 9),
(14, 'img/spectacles/spectacle-9-2.jpg', 9),
(15, 'img/spectacles/spectacle-10-1.jpg', 10),
(16, 'img/spectacles/spectacle-10-2.jpg', 10),
(17, 'img/spectacles/spectacle-10-3.jpg', 10),
(18, 'img/spectacles/spectacle-11-1.jpg', 11),
(19, 'img/spectacles/spectacle-12-1.jpg', 12),
(20, 'img/spectacles/spectacle-12-2.jpg', 12),
(21, 'img/spectacles/spectacle-13-1.jpg', 13),
(22, 'img/spectacles/spectacle-14-1.jpg', 14),
(23, 'img/spectacles/spectacle-15-1.jpg', 15),
(24, 'img/spectacles/spectacle-15-2.jpg', 15),
(25, 'img/spectacles/spectacle-15-3.jpg', 15),
(26, 'img/spectacles/spectacle-16-1.jpg', 16),
(27, 'img/spectacles/spectacle-17-1.jpg', 17),
(28, 'img/spectacles/spectacle-18-1.jpg', 18),
(29, 'img/spectacles/spectacle-18-2.jpg', 18),
(30, 'img/spectacles/spectacle-19-1.jpg', 19),
(31, 'img/spectacles/spectacle-20-1.jpg', 20),
(32, 'img/spectacles/spectacle-20-2.jpg', 20),
(33, 'img/spectacles/spectacle-20-3.jpg', 20),
(34, 'img/spectacles/spectacle-21-1.jpg', 21),
(35, 'img/spectacles/spectacle-21-2.jpg', 21),
(36, 'img/spectacles/spectacle-22-1.jpg', 22),
(37, 'img/spectacles/spectacle-23-1.jpg', 23),
(38, 'img/spectacles/spectacle-24-1.jpg', 24),
(39, 'img/spectacles/spectacle-24-2.jpg', 24),
(40, 'img/spectacles/spectacle-25-1.jpg', 25),
(41, 'img/spectacles/spectacle-25-2.jpg', 25),
(42, 'img/spectacles/spectacle-25-3.jpg', 25);

--  Utilisateurs (mot de passe : nrv2026)
INSERT INTO utilisateur (id, nom, prenom, email, mot_de_passe, role) VALUES
(1, 'Admin', 'NRV', 'admin@nrv.net', '$2y$10$isrKauxXacGG7HSmQMBeR.fnTWRr8MI1Qzw0wWQsYAvUUCKz/zL5G', 1),
(2, 'Bayram', 'Muhammet', 'malik.bayram@mail.fr', '$2y$10$isrKauxXacGG7HSmQMBeR.fnTWRr8MI1Qzw0wWQsYAvUUCKz/zL5G', 0),
(3, 'Thomas', 'Léo', 'leo.thomas@mail.fr', '$2y$10$isrKauxXacGG7HSmQMBeR.fnTWRr8MI1Qzw0wWQsYAvUUCKz/zL5G', 0),
(4, 'Schaeffer', 'Johan', 'johan.schaeffer@mail.fr', '$2y$10$isrKauxXacGG7HSmQMBeR.fnTWRr8MI1Qzw0wWQsYAvUUCKz/zL5G', 0),
(5, 'Durand', 'Loïc', 'loic.durand@mail.fr', '$2y$10$isrKauxXacGG7HSmQMBeR.fnTWRr8MI1Qzw0wWQsYAvUUCKz/zL5G', 0);

INSERT INTO tarif (id, libelle, prix) VALUES
(1, 'Normal', 30.00),
(2, 'Réduit', 20.00);

--  Commandes
INSERT INTO commande (id, id_utilisateur, date_commande, montant_total, statut) VALUES
(1, 2, '2027-03-02 18:12:00', 140.00, 'payee'),
(2, 3, '2027-03-05 09:41:00', 87.30, 'payee'),
(3, 4, '2027-03-10 21:03:00', 260.00, 'payee'),
(4, 2, '2027-03-11 12:30:00', 300.00, 'payee'),
(5, 3, '2027-03-15 17:55:00', 270.00, 'payee'),
(6, 4, '2027-03-16 10:20:00', 160.00, 'payee'),
(7, 2, '2027-04-01 14:00:00', 60.00, 'en_attente');

--  Billets
INSERT INTO billet (id, reference, token, id_commande, id_soiree, id_tarif, prix_paye, est_pass) VALUES
(1, 'NRV-2027-0001', '58f3e5cba3f529c9da3f54f6d23d348f', 1, 1, 1, 30.00, FALSE),
(2, 'NRV-2027-0002', '741fa4af3827e5b9e6301cd453708d2b', 1, 1, 1, 30.00, FALSE),
(3, 'NRV-2027-0003', '9cc149a7344bbdc277120a80b654bfc3', 1, 1, 2, 20.00, FALSE),
(4, 'NRV-2027-0004', 'def068b37f9e6843e33006070a14df59', 1, 2, 1, 30.00, FALSE),
(5, 'NRV-2027-0005', 'f05fe95c24c408e354a9ba4257396efd', 1, 2, 1, 30.00, FALSE),
(6, 'NRV-2027-0006', 'cae00dabed158bde52be071ac7b3d7ca', 2, 1, 1, 29.10, TRUE),
(7, 'NRV-2027-0007', '6f57daceb51c4c872dda5071352d9735', 2, 2, 1, 29.10, TRUE),
(8, 'NRV-2027-0008', 'd139a678d2ffb14ae9aee17e181ce003', 2, 4, 1, 29.10, TRUE),
(9, 'NRV-2027-0009', '3df037b61b0f030f2359f3d249a66157', 3, 5, 1, 30.00, FALSE),
(10, 'NRV-2027-0010', 'fa95cb9be8ddb298ce93eb00ef152eeb', 3, 5, 1, 30.00, FALSE),
(11, 'NRV-2027-0011', '17da212970e2297c860f10ffff356ece', 3, 5, 1, 30.00, FALSE),
(12, 'NRV-2027-0012', 'ca0abb8c806e2ef1d8abe08d8b59bb5f', 3, 5, 1, 30.00, FALSE),
(13, 'NRV-2027-0013', 'd5f4e033a83db55d61c168aace9d242d', 3, 5, 1, 30.00, FALSE),
(14, 'NRV-2027-0014', 'dc79534d54cc92dfeabd907f767034cb', 3, 5, 1, 30.00, FALSE),
(15, 'NRV-2027-0015', '9fdaa3a9860aa6615d44656d1c0c7196', 3, 5, 2, 20.00, FALSE),
(16, 'NRV-2027-0016', '98272bc471cd3644cf6235dd3f0ebae8', 3, 5, 2, 20.00, FALSE),
(17, 'NRV-2027-0017', 'b8c2441f94282cda21815f00160ca254', 3, 5, 2, 20.00, FALSE),
(18, 'NRV-2027-0018', '1d649251d988d9571699d6ee229d041f', 3, 5, 2, 20.00, FALSE),
(19, 'NRV-2027-0019', '1c9726e12e00afdbb31c11e9bcdded2b', 4, 5, 1, 30.00, FALSE),
(20, 'NRV-2027-0020', '441ee87a0f3abfc184926126a080d646', 4, 5, 1, 30.00, FALSE),
(21, 'NRV-2027-0021', '8e546f5b8f84b7967f48b6f810e6550c', 4, 5, 1, 30.00, FALSE),
(22, 'NRV-2027-0022', '35861e0bebcfb7bf4bd6ef8ec412f5e0', 4, 5, 1, 30.00, FALSE),
(23, 'NRV-2027-0023', '24bf67446402106e63e02c6e9fe27c05', 4, 5, 1, 30.00, FALSE),
(24, 'NRV-2027-0024', '1cc15087d2dcb07a26d04a868b14a315', 4, 5, 1, 30.00, FALSE),
(25, 'NRV-2027-0025', 'a5d90c0b5f60ba4859f9cbd2d72cbe76', 4, 5, 1, 30.00, FALSE),
(26, 'NRV-2027-0026', '9d3a3ec74a95ffb596bd2fea6b0a0d31', 4, 5, 1, 30.00, FALSE),
(27, 'NRV-2027-0027', '3e5012bee8ad206e51a3ce0eecdbc19b', 4, 5, 1, 30.00, FALSE),
(28, 'NRV-2027-0028', 'e36709e9194c26a592ccf5047e08d0fc', 4, 5, 1, 30.00, FALSE),
(29, 'NRV-2027-0029', 'b9cf15d5926f8f0f87881b93a7cce38a', 5, 7, 1, 30.00, FALSE),
(30, 'NRV-2027-0030', 'ff13df4c94a1e372d064d8e62e062127', 5, 7, 1, 30.00, FALSE),
(31, 'NRV-2027-0031', 'da23568c2df8a25b60079b39538b7e2a', 5, 7, 1, 30.00, FALSE),
(32, 'NRV-2027-0032', '459fd8a0f28b37a7a639e65eb5d6a366', 5, 7, 1, 30.00, FALSE),
(33, 'NRV-2027-0033', '3371493adacc7a8954546d20580431c4', 5, 7, 1, 30.00, FALSE),
(34, 'NRV-2027-0034', '670495f4ddcd940370d47de073d5ea11', 5, 7, 1, 30.00, FALSE),
(35, 'NRV-2027-0035', '24474a5b1fd572dcb3973d39493fef95', 5, 7, 1, 30.00, FALSE),
(36, 'NRV-2027-0036', '3da4b03cac020f90603b0949f523788c', 5, 7, 1, 30.00, FALSE),
(37, 'NRV-2027-0037', 'e8eeef7e8c5b3f4959bfd89c1fd4ce1c', 5, 7, 1, 30.00, FALSE),
(38, 'NRV-2027-0038', '8a7134f420aa560c4bd7516fe38a19a8', 6, 7, 2, 20.00, FALSE),
(39, 'NRV-2027-0039', '9d397b16753362a0698eef4d68650a03', 6, 7, 2, 20.00, FALSE),
(40, 'NRV-2027-0040', 'eef0cd863e0cee9feca0734d1c3119b7', 6, 7, 2, 20.00, FALSE),
(41, 'NRV-2027-0041', 'e38caaf0355c1b1cab6558a31c4bde1c', 6, 7, 2, 20.00, FALSE),
(42, 'NRV-2027-0042', '62f2f5d21726194386115af37d7e0a25', 6, 7, 2, 20.00, FALSE),
(43, 'NRV-2027-0043', '5215e8a3dc6e06d159544f6cb6255e2b', 6, 7, 2, 20.00, FALSE),
(44, 'NRV-2027-0044', '4df9375d21a28bffcb4785757befb868', 6, 7, 2, 20.00, FALSE),
(45, 'NRV-2027-0045', '1df5b64d78f70fafe37f72e473f2725d', 6, 7, 2, 20.00, FALSE);

-- Host: localhost
-- Gegenereerd op: 19 sep 2019 om 22:57
-- Serverversie: 10.3.14-MariaDB-cll-lve
-- PHP-versie: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bclandegem_database`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_seizoen`
--

CREATE TABLE `intra_seizoen` (
  `id` int(11) NOT NULL,
  `seizoen` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_speeldagen`
--

CREATE TABLE `intra_speeldagen` (
  `id` int(11) NOT NULL,
  `speeldagnummer` int(11) NOT NULL,
  `datum` date NOT NULL,
  `seizoen_id` int(11) NOT NULL,
  `gemiddeld_verliezend` double DEFAULT NULL,
  `is_berekend` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_spelerperseizoen`
--

CREATE TABLE `intra_spelerperseizoen` (
  `id` int(11) NOT NULL,
  `speler_id` int(11) NOT NULL,
  `seizoen_id` int(11) NOT NULL,
  `basispunten` double NOT NULL,
  `gespeelde_sets` int(11) NOT NULL,
  `gewonnen_sets` int(11) NOT NULL,
  `gespeelde_punten` int(11) NOT NULL,
  `gewonnen_punten` int(11) NOT NULL,
  `gespeelde_matchen` int(11) NOT NULL,
  `gewonnen_matchen` int(11) NOT NULL,
  `speeldagen_aanwezig` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_spelerperspeeldag`
--

CREATE TABLE `intra_spelerperspeeldag` (
  `speler_id` int(11) NOT NULL,
  `speeldag_id` int(11) NOT NULL,
  `gemiddelde` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_spelers`
--

CREATE TABLE `intra_spelers` (
  `id` int(11) NOT NULL,
  `voornaam` varchar(32) NOT NULL,
  `naam` varchar(32) NOT NULL,
  `is_lid` tinyint(1) NOT NULL,
  `geslacht` enum('Man','Vrouw') NOT NULL,
  `jeugd` tinyint(1) NOT NULL,
  `is_veteraan` tinyint(1) NOT NULL,
  `klassement` enum('Recreant','D','C2','C1','B2','B1','A') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `intra_wedstrijden`
--

CREATE TABLE `intra_wedstrijden` (
  `id` int(11) NOT NULL,
  `speeldag_id` int(11) NOT NULL,
  `team1_speler1` int(11) NOT NULL,
  `team1_speler2` int(11) NOT NULL,
  `team2_speler1` int(11) NOT NULL,
  `team2_speler2` int(11) NOT NULL,
  `set1_1` int(11) NOT NULL,
  `set1_2` int(11) NOT NULL,
  `set2_1` int(11) NOT NULL,
  `set2_2` int(11) NOT NULL,
  `set3_1` int(11) NOT NULL,
  `set3_2` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `intra_seizoen`
--
ALTER TABLE `intra_seizoen`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `intra_speeldagen`
--
ALTER TABLE `intra_speeldagen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seizoen_id` (`seizoen_id`);

--
-- Indexen voor tabel `intra_spelerperseizoen`
--
ALTER TABLE `intra_spelerperseizoen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `speler_id` (`speler_id`),
  ADD KEY `seizoen_id` (`seizoen_id`);

--
-- Indexen voor tabel `intra_spelerperspeeldag`
--
ALTER TABLE `intra_spelerperspeeldag`
  ADD PRIMARY KEY (`speler_id`,`speeldag_id`),
  ADD KEY `resultatenSpeeldagFK` (`speeldag_id`);

--
-- Indexen voor tabel `intra_spelers`
--
ALTER TABLE `intra_spelers`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `intra_wedstrijden`
--
ALTER TABLE `intra_wedstrijden`
  ADD PRIMARY KEY (`id`),
  ADD KEY `speeldag_id` (`speeldag_id`),
  ADD KEY `team1_speler1` (`team1_speler1`),
  ADD KEY `team1_speler2` (`team1_speler2`),
  ADD KEY `team2_speler1` (`team2_speler1`),
  ADD KEY `team2_speler2` (`team2_speler2`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `intra_seizoen`
--
ALTER TABLE `intra_seizoen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `intra_speeldagen`
--
ALTER TABLE `intra_speeldagen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `intra_spelerperseizoen`
--
ALTER TABLE `intra_spelerperseizoen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `intra_spelers`
--
ALTER TABLE `intra_spelers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `intra_wedstrijden`
--
ALTER TABLE `intra_wedstrijden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `intra_speeldagen`
--
ALTER TABLE `intra_speeldagen`
  ADD CONSTRAINT `speeldagenSeizoenFK` FOREIGN KEY (`seizoen_id`) REFERENCES `intra_seizoen` (`id`) ON DELETE NO ACTION;

--
-- Beperkingen voor tabel `intra_spelerperseizoen`
--
ALTER TABLE `intra_spelerperseizoen`
  ADD CONSTRAINT `resultatenSeizoenFK` FOREIGN KEY (`seizoen_id`) REFERENCES `intra_seizoen` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `resultatenSpelerFK2` FOREIGN KEY (`speler_id`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION;

--
-- Beperkingen voor tabel `intra_spelerperspeeldag`
--
ALTER TABLE `intra_spelerperspeeldag`
  ADD CONSTRAINT `resultatenSpeeldagFK` FOREIGN KEY (`speeldag_id`) REFERENCES `intra_speeldagen` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `resultatenSpelerFK` FOREIGN KEY (`speler_id`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION;

--
-- Beperkingen voor tabel `intra_wedstrijden`
--
ALTER TABLE `intra_wedstrijden`
  ADD CONSTRAINT `team1_speler1FK` FOREIGN KEY (`team1_speler1`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `team1_speler2FK` FOREIGN KEY (`team1_speler2`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `team2_speler1FK` FOREIGN KEY (`team2_speler1`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `team2_speler2FK` FOREIGN KEY (`team2_speler2`) REFERENCES `intra_spelers` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `uitslagenSpeeldagFK` FOREIGN KEY (`speeldag_id`) REFERENCES `intra_speeldagen` (`id`) ON DELETE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

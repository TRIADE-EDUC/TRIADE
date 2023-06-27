-- +-------------------------------------------------+
-- © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
-- +-------------------------------------------------+
-- $Id: indexint_small_pt.sql,v 1.2 2012-12-05 09:41:50 mbertin Exp $

-- MySQL dump 10.13  Distrib 5.1.55, for mandriva-linux-gnu (i586)
--
-- Host: localhost    Database: bibli
-- ------------------------------------------------------
-- Server version	5.1.55-Max

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


truncate table pclassement;
truncate table indexint;

--
-- Dumping data for table `pclassement`
--

LOCK TABLES `pclassement` WRITE;
/*!40000 ALTER TABLE `pclassement` DISABLE KEYS */;
INSERT INTO `pclassement` (`id_pclass`, `name_pclass`, `typedoc`) VALUES (1,'Plan interne','abcdefgijklmr');
/*!40000 ALTER TABLE `pclassement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `indexint`
--

LOCK TABLES `indexint` WRITE;
/*!40000 ALTER TABLE `indexint` DISABLE KEYS */;
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (1,'0','Generalidades. Informação. Organização ',' 0 generalidades informacao organizacao ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (2,'01','Bibliografias. Catálogos ',' 01 bibliografias catalogos ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (3,'02','Bibliotecas. Biblioteconomia ',' 02 bibliotecas biblioteconomia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (4,'030','Ensaios, Panfletos, e Brochuras ',' 030 ensaios panfletos e brochuras ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (5,'040','Publicações Periódicas. Periódicos  ',' 040 publicacoes periodicas periodicos  ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (6,'050','Publicações Periódicas. Periódicos ',' 050 publicacoes periodicas periodicos ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (7,'06','Instituições. Academias. Congressos. Sociedades. Organismos Científicos. Exposições. Museus ','06 instituicoes academias congressos sociedades organismos cientificos exposicoes museus ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (8,'070','Jornais. Jornalismo. Imprensa ',' 070 jornais jornalismo imprensa ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (9,'08','Poligrafias. Poligrafias Colectivas ',' 08 poligrafias poligrafias colectivas ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (10,'09','Manuscritos. Obras Notáveis e Obras Raras ',' 09 manuscritos obras notaveis e obras raras ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (11,'1','Filosofia. Psicologia ',' 1 filosofia psicologia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (12,'11','Metafísica ',' 11 metafisica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (13,'133','Metafísica da vida espiritual. Ocultismo ',' 133 metafisica da vida espiritual ocultismo ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (14,'14','Sistemas e pontos de vista filosóficos ',' 14 sistemas e pontos de vista filosoficos ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (15,'159.1','Psicologia ',' 1591 psicologia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (16,'16','Lógica. Teoria do Conhecimento. Metodologia da Lógica ',' 16 logica teoria do conhecimento metodologia da logica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (17,'17','Filosofia Moral. Ética. Filosofia Prática ',' 17 filosofia moral etica filosofia pratica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (18,'2','Religião. Teologia ',' 2 religiao teologia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (19,'21','Teologia Natural. Teologia Racional. Filosofia Religiosa ',' 21 teologia natural teologia racional filosofia religiosa  ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (20,'22','A Bíblia. Sagradas Escrituras ',' 22 a biblia sagradas escrituras ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (21,'23','Teologia Dogmática ',' 23 teologia dogmatica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (22,'24','Teologia Prática ',' 24 teologia pratica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (23,'25','Teologia Pastoral ',' 25 teologia pastoral ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (24,'26','Igreja Cristã em Geral ',' 26 igreja crista em geral ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (25,'27','História Geral da Igreja Cristã ',' 27 historia geral da igreja crista ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (26,'28','Igrejas Cristãs. Seitas. Denominações (Confissões) ',' 28 igrejas cristas seitas denominacoes confissoes ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (27,'29','Religiões não cristãs ',' 29 religioes nao cristas ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (28,'3','Ciências Sociais. Economia. Direito. Política. Assistência Social. Educação ',' 3 ciencias sociais economia direito politica assistencia social educacao ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (29,'31','Demografia. Sociologia. Estatística ',' 31 demografia sociologia estatistica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (30,'32','Política ',' 32 politica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (31,'33','Economia. Ciência Económica ',' 33 economia ciencia economica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (32,'34','Direito. Jurisprudência ',' 34 direito jurisprudencia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (33,'35','Administração Pública. Governo. Assuntos Militares ',' 35 administracao publica governo assuntos militares ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (34,'36','Assistência Social. Previdência Social. Segurança Social ',' 36 assistencia social previdencia social seguranca social ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (35,'37','Educação ',' 37 educacao ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (36,'38','Metrologia. Pesos e Medidas ',' 38 metrologia pesos e medidas ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (37,'39','Etnologia. Etnografia. Costumes. Modas. Tradições. Folclore ',' 39 etnologia etnografia costumes modas tradicoes folclore ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (38,'4','Classe vaga ',' 4 classe vaga ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (39,'5','Matemática e Ciências Naturais ',' 50 matematica e ciencias naturais ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (40,'50','Generalidades sobre as Ciências Puras ',' 50 generalidades sobre as ciencias puras ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (41,'51','Matemática ',' 51 matematica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (42,'52','Astronomia. Astrofísica. Pesquisa Espacial. Geodesia ',' 52 astronomia astrofisica pesquisa espacial geodesia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (43,'53','Física ',' 53 fisica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (44,'54','Química. Mineralogia ',' 54 quimica mineralogia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (45,'55','Ciências da Terra. Geologia. Meteorologia ',' 55 ciencias da terra geologia meteorologia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (46,'56','Paleontologia ',' 56 paleontologia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (47,'57','Biologia. Antropologia ',' 57 biologia antropologia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (48,'58','Botânica ',' 58 botanica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (49,'59','Zoologia ',' 59 zoologia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (50,'6','Ciências Aplicadas. Medicina. Tecnologia ',' 6 ciencias aplicadas medicina tecnologia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (51,'61','Ciências Médicas ',' 61 ciencias medicas ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (52,'62','Engenharia. Tecnologia em Geral ',' 62 engenharia tecnologia em geral ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (53,'63','Agricultura. Silvicultura. Agronomia. Zootecnia ',' 63 agricultura silvicultura agronomia zootecnia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (54,'64','Ciência Doméstica. Economia Doméstica ',' 64 ciencia domestica economia domestica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (55,'65','Organização e Administração da Indústria, do Comércio e dos Transportes ',' 65 organizacao e administracao da industria do comercio e dos transportes ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (56,'66','Indústria Química. Tecnologia Química ',' 66 industria quimica tecnologia quimica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (57,'67','Indústrias e Ofícios Diversos ',' 67 industrias e oficios diversos ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (58,'68','Indústrias, Artes e Ofícios de Artigos Acabados ',' 68 industrias artes e oficios de artigos acabados ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (59,'69','Engenharia Civil e Estruturas em Geral. Infra-estruturas. Fundações. Construção de Túneis e de Pontes. Superestruturas ',' 69 engenharia civil e estruturas em geral infra-estruturas fundacoes construcao de tuneis e de pontes superestruturas ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (60,'7','Arte. Belas-artes. Recreação. Diversões. Desportos ',' 7 arte belas-artes recreacao diversoes desportos ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (61,'70','Generalidades ',' 70 generalidadest ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (92,'71','Planeamento Regional e Urbano. Paisagens, Jardins, etc ',' 71 planeamento regional e urbano paisagens jardins etc ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (62,'72','Arquitectura ',' 72 arquitectura ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (63,'73','Artes Plásticas. Escultura. Numismática ',' 73 artes plasticas escultura numismatica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (64,'74','Desenho. Artes Industriais ',' 74 desenho artes industriais ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (65,'75','Pintura ',' 75 pintura ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (66,'76','Artes Gráficas ',' 76 artes graficas ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (67,'77','Fotografia e Cinema ',' 77 fotografia e cinema ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (68,'78','Música ',' 78 musica ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (69,'79','Entretenimento. Lazer. Jogos. Desportos ',' 79 entretenimento lazer jogos desportos ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (70,'8','Linguagem. Linguística. Literatura ',' 8 linguagem linguistica literatura ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (71,'80','Linguística. Filologia. Línguas ',' 80 linguistica filologia linguas ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (72,'81','vaga ',' 81 vaga ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (73,'82','Literatura em Língua Inglesa ',' 82 literatura em lingua inglesa ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (74,'83','Literatura Alemã/Escandinava/Holandesa ',' 83 literatura alema escandinava holandesa ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (75,'84','Literatura Francesa ',' 84 literatura francesa ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (76,'85','Literatura Italiana ',' 85 literatura italiana ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (77,'86','Literatura Espanhola/Portuguesa ',' 86 literatura espanhola portuguesa ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (78,'87','Literatura Clássica (Latim e Grego) ',' 87 literatura classica latim e grego ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (79,'88','Literatura Eslava ',' 88 literatura eslava ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (80,'89','Literatura em outras Línguas ',' 89 literatura em outras linguas ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (81,'9','Geografia. Biografia. História ',' 9 geografia biografia historia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (82,'90','Arqueologia. Antiguidades ',' 90 arqueologia antiguidades ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (83,'91','Geografia, Exploração da Terra e Viagens ',' 91 geografia exploracao da terra e viagens ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (84,'929','Biografias ',' 929 biografias ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (85,'93','História ',' 93 historia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (86,'94','História Medieval e Moderna em Geral. História da Europa ',' 94 historia medieval e moderna em geral historia da europa ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (87,'95','História da Ásia ',' 95 historia da asia ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (88,'96','História da África ',' 96 historia da africa ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (89,'97','História da América do Norte e Central ',' 97 historia da america do norte e central ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (90,'98','História da América do Sul ',' 98 historia da america do sul ',1);
INSERT INTO `indexint` (`indexint_id`, `indexint_name`, `indexint_comment`, `index_indexint`, `num_pclass`) VALUES (91,'99','História da Oceânia, dos Territórios Árcticos e da Antárctida ',' 99 historia da oceania dos territorios arcticos e da antarctida ',1);
/*!40000 ALTER TABLE `indexint` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-12-03 15:22:28

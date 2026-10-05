-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 25 août 2026 à 15:16
-- Version du serveur : 11.4.13-MariaDB
-- Version de PHP : 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- --------------------------------------------------------

--
-- Structure de la table `ontology_id2val`
--

CREATE TABLE IF NOT EXISTS `ontology_id2val` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `misc` tinyint(1) NOT NULL DEFAULT 0,
  `val` text NOT NULL,
  `val_type` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`val_type`),
  KEY `v` (`val`(64))
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

--
-- Déchargement des données de la table `ontology_id2val`
--

INSERT INTO `ontology_id2val` VALUES
(1, 0, '', 0),
(3, 0, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 0),
(5, 0, '', 2),
(6, 0, 'http://purl.org/dc/terms/date', 0),
(8, 0, 'http://purl.org/dc/terms/contributor', 0),
(10, 0, 'http://purl.org/dc/terms/creator', 0),
(13, 0, 'http://purl.org/dc/terms/title', 0),
(15, 0, 'fr', 2),
(16, 0, 'http://purl.org/dc/terms/description', 0),
(18, 0, 'http://www.pmbservices.fr/ontology#name', 0),
(21, 0, 'http://www.w3.org/2002/07/owl#disjointWith', 0),
(24, 0, 'http://www.w3.org/2000/01/rdf-schema#label', 0),
(26, 0, 'en', 2),
(27, 0, 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', 0),
(28, 0, 'http://www.w3.org/2004/02/skos/core#definition', 0),
(31, 0, 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 0),
(35, 0, 'http://www.pmbservices.fr/ontology#displayLabel', 0),
(37, 0, 'http://www.pmbservices.fr/ontology#searchLabel', 0),
(42, 0, 'http://www.w3.org/2004/02/skos/core#scopeNote', 0),
(44, 0, 'http://www.w3.org/2004/02/skos/core#example', 0),
(63, 0, 'http://www.w3.org/2000/01/rdf-schema#range', 0),
(64, 0, 'http://www.pmbservices.fr/ontology#datatype', 0),
(67, 0, 'http://www.pmbservices.fr/ontology#defaultValueType', 0),
(69, 0, 'http://www.pmbservices.fr/ontology#defaultValue', 0),
(71, 0, 'http://www.pmbservices.fr/ontology#flag', 0),
(79, 0, 'http://www.w3.org/2000/01/rdf-schema#domain', 0),
(80, 0, 'http://www.w3.org/2002/07/owl#inverseOf', 0),
(85, 0, 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf', 0),
(90, 0, 'http://www.w3.org/2000/01/rdf-schema#comment', 0),
(95, 0, 'http://www.pmbservices.fr/ontology#distinctWith', 0),
(99, 0, 'http://www.pmbservices.fr/ontology#pound', 0),
(180, 0, 'http://www.w3.org/2002/07/owl#unionOf', 0),
(182, 0, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first', 0),
(183, 0, 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest', 0),
(220, 0, 'http://www.w3.org/2002/07/owl#onProperty', 0),
(221, 0, 'http://www.w3.org/2002/07/owl#maxCardinality', 0),
(223, 0, 'http://www.w3.org/2001/XMLSchema#nonNegativeInteger', 0),
(224, 0, 'http://www.w3.org/2002/07/owl#minCardinality', 0),
(226, 0, 'http://www.pmbservices.fr/ontology#field', 0),
(227, 0, 'http://www.pmbservices.fr/ontology#subfield', 0),
(233, 0, 'http://www.pmbservices.fr/ontology#useProperty', 0),
(237, 0, 'http://www.w3.org/2002/07/owl#onProper3ty', 0);

-- --------------------------------------------------------

--
-- Structure de la table `ontology_o2val`
--

CREATE TABLE IF NOT EXISTS `ontology_o2val` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `misc` tinyint(1) NOT NULL DEFAULT 0,
  `val_hash` char(32) NOT NULL,
  `val` text NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `vh` (`val_hash`),
  KEY `v` (`val`(64))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

--
-- Déchargement des données de la table `ontology_o2val`
--

INSERT INTO `ontology_o2val` VALUES
(4, 0, '996223817', 'http://www.w3.org/2002/07/owl#Ontology'),
(7, 0, '2435460757', '2014-04-22T10:20:37+02:00'),
(9, 0, '409221262', 'Florent Tétart'),
(11, 0, '3360724324', 'Matthieu Bertin'),
(12, 0, '115883339', 'Didier Bellamy'),
(14, 0, '2959168336', 'Vocabulaire SKOS - PMB'),
(17, 0, '3688488397', 'Ontologie PMB basée sur Skos'),
(19, 0, '925213505', 'skos'),
(22, 0, '2442742997', 'http://www.w3.org/2004/02/skos/core#ConceptScheme'),
(23, 0, '2854212235', 'http://www.w3.org/2004/02/skos/core#Collection'),
(25, 0, '687299020', 'Concept'),
(2, 0, '3622535672', 'http://www.w3.org/2004/02/skos/core'),
(29, 0, '821429949', 'An idea or notion; a unit of thought.'),
(30, 0, '3775447655', 'http://www.w3.org/2002/07/owl#Class'),
(32, 0, '2687605415', '_:b1802443385_inscheme'),
(33, 0, '348940856', '_:b2056747303_nceptindex'),
(34, 0, '1760774299', '_:b2982083295_preflabel'),
(36, 0, '2503630580', 'http://www.w3.org/2004/02/skos/core#prefLabel'),
(38, 0, '687264230', 'http://www.w3.org/2004/02/skos/core#altLabel'),
(39, 0, '3880411216', 'concept'),
(40, 0, '3841089632', 'Concept Scheme'),
(41, 0, '1665358631', 'A set of concepts, optionally including statements about semantic relationships between those concepts.'),
(43, 0, '3538028512', 'A concept scheme may be defined to include concepts from different sources.'),
(45, 0, '3830592684', 'Thesauri, classification schemes, subject heading lists, taxonomies, \'folksonomies\', and other types of controlled vocabulary are all examples of concept schemes. Concept schemes are also embedded in glossaries and terminologies.'),
(20, 0, '3808008927', 'http://www.w3.org/2004/02/skos/core#Concept'),
(46, 0, '2325969209', '_:b1380285311_chemeindex'),
(47, 0, '1918600333', 'conceptscheme'),
(48, 0, '3004196578', 'Collection'),
(49, 0, '3470114471', 'A meaningful collection of concepts.'),
(50, 0, '2718693607', 'Labelled collections can be used where you would like a set of concepts to be displayed under a \'node label\' in the hierarchy.'),
(51, 0, '4232930610', 'collection'),
(53, 0, '3794253576', 'Ordered Collection'),
(54, 0, '2110881773', 'An ordered collection of concepts, where both the grouping and the ordering are meaningful.'),
(55, 0, '1228035159', 'Ordered collections can be used where you would like a set of concepts to be displayed in a specific order, and optionally under a \'node label\'.'),
(56, 0, '1910634737', 'orderedcollection'),
(58, 0, '311312342', 'is in scheme'),
(59, 0, '3640587640', 'Relates a resource (for example a concept) to a concept scheme in which it is included.'),
(60, 0, '1181726777', 'A concept may be a member of more than one concept scheme.'),
(61, 0, '1929034498', 'http://www.w3.org/2002/07/owl#ObjectProperty'),
(62, 0, '3589091542', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'),
(65, 0, '3294050761', 'http://www.pmbservices.fr/ontology#resource_selector'),
(66, 0, '2133751721', 'inscheme'),
(68, 0, '837312391', 'http://www.pmbservices.fr/ontology#variable'),
(70, 0, '874167628', 'concept_scheme'),
(72, 0, '942077639', 'concept_selector_form'),
(73, 0, '2054802361', 'conceptscheme_selector_form'),
(74, 0, '2985665616', 'collection_selector_form'),
(75, 0, '2643917610', 'orderedcollection_selector_form'),
(77, 0, '1230851919', 'has top concept'),
(78, 0, '1519685800', 'Relates, by convention, a concept scheme to a concept which is topmost in the broader/narrower concept hierarchies for that scheme, providing an entry point to these hierarchies.'),
(81, 0, '282586597', 'http://www.w3.org/2004/02/skos/core#topConceptOf'),
(82, 0, '633150989', 'hastopconcept'),
(83, 0, '2529889121', 'is top concept in scheme'),
(84, 0, '3223040500', 'Relates a concept to the concept scheme that it is a top level concept of.'),
(57, 0, '4243996034', 'http://www.w3.org/2004/02/skos/core#inScheme'),
(76, 0, '4065174234', 'http://www.w3.org/2004/02/skos/core#hasTopConcept'),
(86, 0, '2140272016', 'topconceptof'),
(87, 0, '730656352', 'preferred label'),
(88, 0, '182995678', 'The preferred lexical label for a resource, in a given language.'),
(89, 0, '3851153107', 'http://www.w3.org/2002/07/owl#AnnotationProperty'),
(24, 0, '10943426', 'http://www.w3.org/2000/01/rdf-schema#label'),
(91, 0, '3498468806', 'A resource has no more than one value of skos:prefLabel per language tag, and no more than one value of skos:prefLabel without language tag.'),
(92, 0, '337617011', 'The range of skos:prefLabel is the class of RDF plain literals.'),
(93, 0, '3309638314', 'skos:prefLabel, skos:altLabel and skos:hiddenLabel are pairwise\n      disjoint properties.'),
(94, 0, '1886301245', 'http://www.w3.org/2000/01/rdf-schema#Literal'),
(96, 0, '789562387', 'http://www.w3.org/2004/02/skos/core#hiddenLabel'),
(97, 0, '2933003907', 'http://www.pmbservices.fr/ontology#small_text'),
(98, 0, '930703718', 'preflabel'),
(100, 0, '140116777', '130'),
(101, 0, '3740932510', 'alternative label'),
(102, 0, '2086577230', 'An alternative lexical label for a resource.'),
(103, 0, '2400479509', 'Acronyms, abbreviations, spelling variants, and irregular plural/singular forms may be included among the alternative labels for a concept. Mis-spelled terms are normally included as hidden labels (see skos:hiddenLabel).'),
(104, 0, '2635350085', 'The range of skos:altLabel is the class of RDF plain literals.'),
(105, 0, '1361526975', 'skos:prefLabel, skos:altLabel and skos:hiddenLabel are pairwise disjoint properties.'),
(106, 0, '1833153023', 'altlabel'),
(107, 0, '725236514', 'hidden label'),
(108, 0, '2400639206', 'A lexical label for a resource that should be hidden when generating visual displays of the resource, but should still be accessible to free text search operations.'),
(109, 0, '3747991246', 'The range of skos:hiddenLabel is the class of RDF plain literals.'),
(110, 0, '3669572959', 'hiddenlabel'),
(112, 0, '40418453', 'notation'),
(113, 0, '994713163', 'A notation, also known as classification code, is a string of characters such as \"T58.5\" or \"303.4833\" used to uniquely identify a concept within the scope of a given concept scheme.'),
(114, 0, '2413086594', 'By convention, skos:notation is used with a typed literal in the object position of the triple.'),
(115, 0, '2882651566', 'http://www.w3.org/2002/07/owl#DatatypeProperty'),
(117, 0, '3485334036', 'note'),
(118, 0, '2265513567', 'A general note, for any purpose.'),
(119, 0, '532756817', 'This property may be used directly, or as a super-property for more specific note types.'),
(120, 0, '3629424921', 'http://www.pmbservices.fr/ontology#text'),
(122, 0, '3622356145', 'change note'),
(123, 0, '2667132589', 'A note about a modification to a concept.'),
(116, 0, '3906281882', 'http://www.w3.org/2004/02/skos/core#note'),
(124, 0, '1408467443', 'changenote'),
(125, 0, '1747988440', 'definition'),
(126, 0, '2210453250', 'A statement or formal explanation of the meaning of a concept.'),
(128, 0, '2690503393', 'editorial note'),
(129, 0, '2932220503', 'A note for an editor, translator or maintainer of the vocabulary.'),
(130, 0, '3788851186', 'editorialnote'),
(131, 0, '1861000095', 'example'),
(132, 0, '248199406', 'An example of the use of a concept.'),
(134, 0, '3205168502', 'history note'),
(135, 0, '2488806761', 'A note about the past state/use/meaning of a concept.'),
(136, 0, '1819917857', 'historynote'),
(137, 0, '163061077', 'scope note'),
(138, 0, '3898045383', 'A note that helps to clarify the meaning and/or the use of a concept.'),
(139, 0, '3098845214', 'scopenote'),
(141, 0, '1089314813', 'is in semantic relation with'),
(142, 0, '1802313554', 'Links a concept to a concept related by meaning.'),
(143, 0, '2290258691', 'This property should not be used directly, but as a super-property for all properties denoting a relationship of meaning between concepts.'),
(144, 0, '385810402', 'http://www.pmbservices.fr/ontology#noAssertionProperty'),
(145, 0, '1136779235', 'semanticrelation'),
(147, 0, '2760464747', 'has broader'),
(148, 0, '3148668097', 'Relates a concept to a concept that is more general in meaning.'),
(149, 0, '1304010208', 'Broader concepts are typically rendered as parents in a concept hierarchy (tree).'),
(150, 0, '324817978', 'By convention, skos:broader is only used to assert an immediate (i.e. direct) hierarchical link between two conceptual resources.'),
(151, 0, '2386912048', 'http://www.w3.org/2004/02/skos/core#broaderTransitive'),
(152, 0, '1484025019', 'http://www.w3.org/2004/02/skos/core#narrower'),
(153, 0, '2858005379', 'http://www.w3.org/2004/02/skos/core#related'),
(154, 0, '1090252510', 'http://www.w3.org/2004/02/skos/core#narrowerTransitive'),
(155, 0, '3054023308', 'broader'),
(156, 0, '1920649840', 'parent_id'),
(157, 0, '3799831958', 'has narrower'),
(158, 0, '1680599707', 'Relates a concept to a concept that is more specific in meaning.'),
(159, 0, '2730100506', 'Narrower concepts are typically rendered as children in a concept hierarchy (tree).'),
(146, 0, '2080797087', 'http://www.w3.org/2004/02/skos/core#broader'),
(160, 0, '3690996646', 'narrower'),
(161, 0, '1926667127', 'has related'),
(162, 0, '2879986832', 'Relates a concept to a concept with which there is an associative semantic relationship.'),
(163, 0, '1240048252', 'http://www.w3.org/2002/07/owl#SymmetricProperty'),
(140, 0, '1000326490', 'http://www.w3.org/2004/02/skos/core#semanticRelation'),
(164, 0, '171165871', 'skos:related is disjoint with skos:broaderTransitive'),
(165, 0, '1616343184', 'related'),
(166, 0, '659776027', 'has broader transitive'),
(167, 0, '3974536664', 'skos:broaderTransitive is a transitive superproperty of skos:broader.'),
(168, 0, '1356335522', 'By convention, skos:broaderTransitive is not used to make assertions. Rather, the properties can be used to draw inferences about the transitive closure of the hierarchical relation, which is useful e.g. when implementing a simple query expansion algorithm in a search application.'),
(169, 0, '4239053696', 'http://www.w3.org/2002/07/owl#TransitiveProperty'),
(170, 0, '4046239601', 'broadertransitive'),
(171, 0, '2863590464', 'has narrower transitive'),
(172, 0, '3450257967', 'skos:narrowerTransitive is a transitive superproperty of skos:narrower.'),
(173, 0, '1192918391', 'By convention, skos:narrowerTransitive is not used to make assertions. Rather, the properties can be used to draw inferences about the transitive closure of the hierarchical relation, which is useful e.g. when implementing a simple query expansion algorithm in a search application.'),
(174, 0, '2292087307', 'narrowertransitive'),
(176, 0, '1189498145', 'has member'),
(177, 0, '3812418564', 'Relates a collection to one of its members.'),
(178, 0, '3830759593', '_:b3760457392__arc92ccb1'),
(179, 0, '1894054520', 'member'),
(181, 0, '4024289844', '_:b2331651819__arc92ccb2'),
(184, 0, '2446578601', '_:b1717355975__arc92ccb3'),
(185, 0, '3145999675', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil'),
(187, 0, '4244389336', 'has member list'),
(188, 0, '2512561502', 'Relates an ordered collection to the RDF list containing its members.'),
(189, 0, '3708180253', 'http://www.w3.org/2002/07/owl#FunctionalProperty'),
(52, 0, '539696714', 'http://www.w3.org/2004/02/skos/core#OrderedCollection'),
(190, 0, '4089142914', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#List'),
(191, 0, '3377153846', 'For any resource, every item in the list given as the value of the\n      skos:memberList property is also a value of the skos:member property.'),
(192, 0, '940032441', 'memberlist'),
(194, 0, '2520904950', 'is in mapping relation with'),
(195, 0, '156766410', 'Relates two concepts coming, by convention, from different schemes, and that have comparable meanings'),
(196, 0, '1652448020', 'These concept mapping relations mirror semantic relations, and the data model defined below is similar (with the exception of skos:exactMatch) to the data model defined for semantic relations. A distinct vocabulary is provided for concept mapping relations, to provide a convenient way to differentiate links within a concept scheme from links between concept schemes. However, this pattern of usage is not a formal requirement of the SKOS data model, and relies on informal definitions of best practice.'),
(197, 0, '1032933140', 'mappingrelation'),
(199, 0, '319277219', 'has broader match'),
(200, 0, '1020696399', 'skos:broadMatch is used to state a hierarchical mapping link between two conceptual resources in different concept schemes.'),
(193, 0, '1384714134', 'http://www.w3.org/2004/02/skos/core#mappingRelation'),
(201, 0, '1161221123', 'http://www.w3.org/2004/02/skos/core#narrowMatch'),
(202, 0, '3441743472', 'http://www.w3.org/2004/02/skos/core#exactMatch'),
(203, 0, '1973321835', 'http://www.w3.org/2004/02/skos/core#relatedMatch'),
(204, 0, '2345200264', 'http://www.w3.org/2004/02/skos/core#closeMatch'),
(205, 0, '3822837114', 'broadmatch'),
(206, 0, '196249616', 'has narrower match'),
(207, 0, '184740193', 'skos:narrowMatch is used to state a hierarchical mapping link between two conceptual resources in different concept schemes.'),
(198, 0, '991249943', 'http://www.w3.org/2004/02/skos/core#broadMatch'),
(208, 0, '2961190223', 'narrowmatch'),
(209, 0, '4004308878', 'has related match'),
(210, 0, '1760804343', 'skos:relatedMatch is used to state an associative mapping link between two conceptual resources in different concept schemes.'),
(211, 0, '3429634331', 'relatedmatch'),
(212, 0, '640295377', 'has exact match'),
(213, 0, '1943197459', 'skos:exactMatch is used to link two concepts, indicating a high degree of confidence that the concepts can be used interchangeably across a wide range of information retrieval applications. skos:exactMatch is a transitive property, and is a sub-property of skos:closeMatch.'),
(214, 0, '1079909107', 'skos:exactMatch is disjoint with each of the properties skos:broadMatch and skos:relatedMatch.'),
(215, 0, '367676701', 'exactmatch'),
(216, 0, '2500507587', 'has close match'),
(217, 0, '346057643', 'skos:closeMatch is used to link two concepts that are sufficiently similar that they can be used interchangeably in some information retrieval applications. In order to avoid the possibility of \"compound errors\" when combining mappings across more than two concept schemes, skos:closeMatch is not declared to be a transitive property.'),
(218, 0, '1392931301', 'closematch'),
(219, 0, '4257628229', 'http://www.w3.org/2002/07/owl#Restriction'),
(222, 0, '2212294583', '1'),
(225, 0, '218542642', 'http://www.pmbservices.fr/ontology#indexation'),
(228, 0, '495674787', '_:b3728233012_eptindex_1'),
(229, 0, '450215437', '2'),
(230, 0, '3538404368', '_:b1194394510_eptindex_2'),
(231, 0, '1842515611', '3'),
(232, 0, '1868773733', '_:b808858392_eptindex_3'),
(234, 0, '2473281379', '30'),
(235, 0, '4088798008', '4'),
(236, 0, '2225363587', '_:b1079822231_eptindex_6'),
(111, 0, '2250044296', 'http://www.w3.org/2004/02/skos/core#notation'),
(238, 0, '1790921346', '7'),
(239, 0, '87504613', '_:b928773889_eptindex_7'),
(240, 0, '4194326291', '8'),
(241, 0, '2626955311', '_:b2816797328_eptindex_8'),
(121, 0, '3939408548', 'http://www.w3.org/2004/02/skos/core#changeNote'),
(242, 0, '2366072709', '9'),
(243, 0, '2472740986', '_:b3504593414_eptindex_9'),
(28, 0, '1899994033', 'http://www.w3.org/2004/02/skos/core#definition'),
(244, 0, '2707236321', '10'),
(245, 0, '1938729608', '_:b3585151942_ptindex_10'),
(127, 0, '385314391', 'http://www.w3.org/2004/02/skos/core#editorialNote'),
(246, 0, '3596227959', '11'),
(247, 0, '715255915', '_:b2729845584_ptindex_11'),
(44, 0, '2766297228', 'http://www.w3.org/2004/02/skos/core#example'),
(248, 0, '1330857165', '12'),
(249, 0, '2558142928', '_:b1002402538_ptindex_12'),
(133, 0, '4164732759', 'http://www.w3.org/2004/02/skos/core#historyNote'),
(250, 0, '945058907', '13'),
(251, 0, '1426967761', '_:b1287144060_ptindex_13'),
(42, 0, '2063865782', 'http://www.w3.org/2004/02/skos/core#scopeNote'),
(252, 0, '2788221432', '14'),
(253, 0, '595022058', '100'),
(254, 0, '2547653827', '_:b1112853654_emeindex_1'),
(255, 0, '1416650876', '101'),
(256, 0, '794365474', '_:b3680345388_emeindex_2'),
(257, 0, '3447271878', '102'),
(258, 0, '1072067885', '_:b2891623866_emeindex_3'),
(259, 0, '3994858278', '60'),
(260, 0, '3128820048', '103'),
(261, 0, '3106712262', '_:b324127774_meindex_4\''),
(263, 0, '605721843', '104');

-- --------------------------------------------------------

--
-- Structure de la table `ontology_s2val`
--

CREATE TABLE IF NOT EXISTS `ontology_s2val` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `misc` tinyint(1) NOT NULL DEFAULT 0,
  `val_hash` char(32) NOT NULL,
  `val` text NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `vh` (`val_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

--
-- Déchargement des données de la table `ontology_s2val`
--

INSERT INTO `ontology_s2val` VALUES
(2, 0, '3622535672', 'http://www.w3.org/2004/02/skos/core'),
(20, 0, '3808008927', 'http://www.w3.org/2004/02/skos/core#Concept'),
(22, 0, '2442742997', 'http://www.w3.org/2004/02/skos/core#ConceptScheme'),
(23, 0, '2854212235', 'http://www.w3.org/2004/02/skos/core#Collection'),
(52, 0, '539696714', 'http://www.w3.org/2004/02/skos/core#OrderedCollection'),
(57, 0, '4243996034', 'http://www.w3.org/2004/02/skos/core#inScheme'),
(76, 0, '4065174234', 'http://www.w3.org/2004/02/skos/core#hasTopConcept'),
(81, 0, '282586597', 'http://www.w3.org/2004/02/skos/core#topConceptOf'),
(36, 0, '2503630580', 'http://www.w3.org/2004/02/skos/core#prefLabel'),
(38, 0, '687264230', 'http://www.w3.org/2004/02/skos/core#altLabel'),
(96, 0, '789562387', 'http://www.w3.org/2004/02/skos/core#hiddenLabel'),
(111, 0, '2250044296', 'http://www.w3.org/2004/02/skos/core#notation'),
(116, 0, '3906281882', 'http://www.w3.org/2004/02/skos/core#note'),
(121, 0, '3939408548', 'http://www.w3.org/2004/02/skos/core#changeNote'),
(28, 0, '1899994033', 'http://www.w3.org/2004/02/skos/core#definition'),
(127, 0, '385314391', 'http://www.w3.org/2004/02/skos/core#editorialNote'),
(44, 0, '2766297228', 'http://www.w3.org/2004/02/skos/core#example'),
(133, 0, '4164732759', 'http://www.w3.org/2004/02/skos/core#historyNote'),
(42, 0, '2063865782', 'http://www.w3.org/2004/02/skos/core#scopeNote'),
(140, 0, '1000326490', 'http://www.w3.org/2004/02/skos/core#semanticRelation'),
(146, 0, '2080797087', 'http://www.w3.org/2004/02/skos/core#broader'),
(152, 0, '1484025019', 'http://www.w3.org/2004/02/skos/core#narrower'),
(153, 0, '2858005379', 'http://www.w3.org/2004/02/skos/core#related'),
(151, 0, '2386912048', 'http://www.w3.org/2004/02/skos/core#broaderTransitive'),
(154, 0, '1090252510', 'http://www.w3.org/2004/02/skos/core#narrowerTransitive'),
(175, 0, '3303367561', 'http://www.w3.org/2004/02/skos/core#member'),
(178, 0, '3830759593', '_:b3760457392__arc92ccb1'),
(181, 0, '4024289844', '_:b2331651819__arc92ccb2'),
(184, 0, '2446578601', '_:b1717355975__arc92ccb3'),
(186, 0, '2168117998', 'http://www.w3.org/2004/02/skos/core#memberList'),
(193, 0, '1384714134', 'http://www.w3.org/2004/02/skos/core#mappingRelation'),
(198, 0, '991249943', 'http://www.w3.org/2004/02/skos/core#broadMatch'),
(201, 0, '1161221123', 'http://www.w3.org/2004/02/skos/core#narrowMatch'),
(203, 0, '1973321835', 'http://www.w3.org/2004/02/skos/core#relatedMatch'),
(202, 0, '3441743472', 'http://www.w3.org/2004/02/skos/core#exactMatch'),
(204, 0, '2345200264', 'http://www.w3.org/2004/02/skos/core#closeMatch'),
(34, 0, '1760774299', '_:b2982083295_preflabel'),
(33, 0, '348940856', '_:b2056747303_nceptindex'),
(228, 0, '495674787', '_:b3728233012_eptindex_1'),
(230, 0, '3538404368', '_:b1194394510_eptindex_2'),
(232, 0, '1868773733', '_:b808858392_eptindex_3'),
(236, 0, '2225363587', '_:b1079822231_eptindex_6'),
(239, 0, '87504613', '_:b928773889_eptindex_7'),
(241, 0, '2626955311', '_:b2816797328_eptindex_8'),
(243, 0, '2472740986', '_:b3504593414_eptindex_9'),
(245, 0, '1938729608', '_:b3585151942_ptindex_10'),
(247, 0, '715255915', '_:b2729845584_ptindex_11'),
(249, 0, '2558142928', '_:b1002402538_ptindex_12'),
(251, 0, '1426967761', '_:b1287144060_ptindex_13'),
(46, 0, '2325969209', '_:b1380285311_chemeindex'),
(254, 0, '2547653827', '_:b1112853654_emeindex_1'),
(256, 0, '794365474', '_:b3680345388_emeindex_2'),
(258, 0, '1072067885', '_:b2891623866_emeindex_3'),
(262, 0, '2833653488', '_:b842938393_emeindex_4');

-- --------------------------------------------------------

--
-- Structure de la table `ontology_setting`
--

CREATE TABLE IF NOT EXISTS `ontology_setting` (
  `k` char(32) NOT NULL,
  `val` text NOT NULL,
  UNIQUE KEY `k` (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Structure de la table `ontology_triple`
--

CREATE TABLE IF NOT EXISTS `ontology_triple` (
  `t` mediumint(8) UNSIGNED NOT NULL,
  `s` mediumint(8) UNSIGNED NOT NULL,
  `p` mediumint(8) UNSIGNED NOT NULL,
  `o` mediumint(8) UNSIGNED NOT NULL,
  `o_lang_dt` mediumint(8) UNSIGNED NOT NULL,
  `o_comp` char(35) NOT NULL,
  `s_type` tinyint(1) NOT NULL DEFAULT 0,
  `o_type` tinyint(1) NOT NULL DEFAULT 0,
  `misc` tinyint(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `t` (`t`),
  KEY `sp` (`s`,`p`),
  KEY `os` (`o`,`s`),
  KEY `po` (`p`,`o`),
  KEY `misc` (`misc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

--
-- Déchargement des données de la table `ontology_triple`
--

INSERT INTO `ontology_triple` VALUES
(1, 2, 3, 4, 5, 'http://www.w3.org-2/07/owl#Ontology', 0, 0, 0),
(2, 2, 6, 7, 5, '2014-04-22T10:20:37+02:00', 0, 2, 0),
(3, 2, 8, 9, 5, 'Florent-Tétart', 0, 2, 0),
(4, 2, 10, 11, 5, 'Matthieu-Bertin', 0, 2, 0),
(5, 2, 10, 12, 5, 'Didier-Bellamy', 0, 2, 0),
(6, 2, 13, 14, 15, 'Vocabulaire-SKOS---PMB', 0, 2, 0),
(7, 2, 16, 17, 15, 'Ontologie-PMB-basée-sur-Skos', 0, 2, 0),
(8, 2, 18, 19, 5, 'skos', 0, 2, 0),
(9, 20, 21, 22, 5, 'http://www.w3.org-ore#ConceptScheme', 0, 0, 0),
(10, 20, 21, 23, 5, 'http://www.w3.org-s/core#Collection', 0, 0, 0),
(11, 20, 24, 25, 26, 'Concept', 0, 2, 0),
(12, 20, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(13, 20, 28, 29, 26, 'An-idea-or-notion--unit-of-thought.', 0, 2, 0),
(14, 20, 3, 30, 5, 'http://www.w3.org/2002/07/owl#Class', 0, 0, 0),
(15, 20, 31, 32, 5, '_:b1802443385_inscheme', 0, 1, 0),
(16, 20, 31, 33, 5, '_:b2056747303_nceptindex', 0, 1, 0),
(17, 20, 31, 34, 5, '_:b2982083295_preflabel', 0, 1, 0),
(18, 20, 35, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(19, 20, 37, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(20, 20, 37, 38, 5, 'http://www.w3.org-kos/core#altLabel', 0, 0, 0),
(21, 20, 18, 39, 5, 'concept', 0, 2, 0),
(22, 22, 24, 40, 26, 'Concept-Scheme', 0, 2, 0),
(23, 22, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(24, 22, 28, 41, 26, 'A-set-of-concepts-n-those-concepts.', 0, 2, 0),
(25, 22, 42, 43, 26, 'A-concept-scheme--ifferent-sources.', 0, 2, 0),
(26, 22, 44, 45, 26, 'Thesauri,-classif-nd-terminologies.', 0, 2, 0),
(27, 22, 3, 30, 5, 'http://www.w3.org/2002/07/owl#Class', 0, 0, 0),
(28, 22, 21, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(29, 22, 21, 23, 5, 'http://www.w3.org-s/core#Collection', 0, 0, 0),
(30, 22, 31, 32, 5, '_:b1802443385_inscheme', 0, 1, 0),
(31, 22, 31, 34, 5, '_:b2982083295_preflabel', 0, 1, 0),
(32, 22, 31, 46, 5, '_:b1380285311_chemeindex', 0, 1, 0),
(33, 22, 35, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(34, 22, 37, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(35, 22, 37, 38, 5, 'http://www.w3.org-kos/core#altLabel', 0, 0, 0),
(36, 22, 18, 47, 5, 'conceptscheme', 0, 2, 0),
(37, 23, 24, 48, 26, 'Collection', 0, 2, 0),
(38, 23, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(39, 23, 28, 49, 26, 'A-meaningful-coll-tion-of-concepts.', 0, 2, 0),
(40, 23, 42, 50, 26, 'Labelled-collecti-in-the-hierarchy.', 0, 2, 0),
(41, 23, 3, 30, 5, 'http://www.w3.org/2002/07/owl#Class', 0, 0, 0),
(42, 23, 21, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(43, 23, 21, 22, 5, 'http://www.w3.org-ore#ConceptScheme', 0, 0, 0),
(44, 23, 31, 32, 5, '_:b1802443385_inscheme', 0, 1, 0),
(45, 23, 31, 34, 5, '_:b2982083295_preflabel', 0, 1, 0),
(46, 23, 35, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(47, 23, 37, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(48, 23, 37, 38, 5, 'http://www.w3.org-kos/core#altLabel', 0, 0, 0),
(49, 23, 18, 51, 5, 'collection', 0, 2, 0),
(50, 52, 24, 53, 26, 'Ordered-Collection', 0, 2, 0),
(51, 52, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(52, 52, 28, 54, 26, 'An-ordered-collec-g-are-meaningful.', 0, 2, 0),
(53, 52, 42, 55, 26, 'Ordered-collectio-er-a-node-label-.', 0, 2, 0),
(54, 52, 3, 30, 5, 'http://www.w3.org/2002/07/owl#Class', 0, 0, 0),
(55, 52, 31, 23, 5, 'http://www.w3.org-s/core#Collection', 0, 0, 0),
(56, 52, 31, 32, 5, '_:b1802443385_inscheme', 0, 1, 0),
(57, 52, 31, 34, 5, '_:b2982083295_preflabel', 0, 1, 0),
(58, 52, 35, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(59, 52, 37, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(60, 52, 37, 38, 5, 'http://www.w3.org-kos/core#altLabel', 0, 0, 0),
(61, 52, 18, 56, 5, 'orderedcollection', 0, 2, 0),
(62, 57, 24, 58, 26, 'is-in-scheme', 0, 2, 0),
(63, 57, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(64, 57, 28, 59, 26, 'Relates-a-resourc-h-it-is-included.', 0, 2, 0),
(65, 57, 42, 60, 26, 'A-concept-may-be--e-concept-scheme.', 0, 2, 0),
(66, 57, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(67, 57, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(68, 57, 63, 22, 5, 'http://www.w3.org-ore#ConceptScheme', 0, 0, 0),
(69, 57, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(70, 57, 18, 66, 5, 'inscheme', 0, 2, 0),
(71, 57, 67, 68, 5, 'http://www.pmbser-ontology#variable', 0, 0, 0),
(72, 57, 69, 70, 5, 'concept_scheme', 0, 2, 0),
(73, 57, 71, 72, 5, 'concept_selector_form', 0, 2, 0),
(74, 57, 71, 73, 5, 'conceptscheme_selector_form', 0, 2, 0),
(75, 57, 71, 74, 5, 'collection_selector_form', 0, 2, 0),
(76, 57, 71, 75, 5, 'orderedcollection_selector_form', 0, 2, 0),
(77, 76, 24, 77, 26, 'has-top-concept', 0, 2, 0),
(78, 76, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(79, 76, 28, 78, 26, 'Relates,-by-conve-hese-hierarchies.', 0, 2, 0),
(80, 76, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(81, 76, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(82, 76, 79, 22, 5, 'http://www.w3.org-ore#ConceptScheme', 0, 0, 0),
(83, 76, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(84, 76, 80, 81, 5, 'http://www.w3.org-core#topConceptOf', 0, 0, 0),
(85, 76, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(86, 76, 18, 82, 5, 'hastopconcept', 0, 2, 0),
(87, 81, 24, 83, 26, 'is-top-concept-in-scheme', 0, 2, 0),
(88, 81, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(89, 81, 28, 84, 26, 'Relates-a-concept-level-concept-of.', 0, 2, 0),
(90, 81, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(91, 81, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(92, 81, 85, 57, 5, 'http://www.w3.org-kos/core#inScheme', 0, 0, 0),
(93, 81, 80, 76, 5, 'http://www.w3.org-ore#hasTopConcept', 0, 0, 0),
(94, 81, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(95, 81, 63, 22, 5, 'http://www.w3.org-ore#ConceptScheme', 0, 0, 0),
(96, 81, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(97, 81, 18, 86, 5, 'topconceptof', 0, 2, 0),
(98, 36, 24, 87, 26, 'preferred-label', 0, 2, 0),
(99, 36, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(100, 36, 28, 88, 26, 'The-preferred-lex-a-given-language.', 0, 2, 0),
(101, 36, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(102, 36, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(103, 36, 85, 24, 5, 'http://www.w3.org-/rdf-schema#label', 0, 0, 0),
(104, 36, 90, 91, 26, 'A-resource-has-no-out-language-tag.', 0, 2, 0),
(105, 36, 90, 92, 26, 'The-range-of-skos-F-plain-literals.', 0, 2, 0),
(106, 36, 90, 93, 26, 'skos:prefLabel,-s-joint-properties.', 0, 2, 0),
(107, 36, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(108, 36, 95, 38, 5, 'http://www.w3.org-kos/core#altLabel', 0, 0, 0),
(109, 36, 95, 96, 5, 'http://www.w3.org-/core#hiddenLabel', 0, 0, 0),
(110, 36, 64, 97, 5, 'http://www.pmbser-tology#small_text', 0, 0, 0),
(111, 36, 18, 98, 5, 'preflabel', 0, 2, 0),
(112, 36, 99, 100, 5, '+000000000000000130.000000000000000', 0, 2, 0),
(113, 36, 71, 72, 5, 'concept_selector_form', 0, 2, 0),
(114, 36, 71, 73, 5, 'conceptscheme_selector_form', 0, 2, 0),
(115, 36, 71, 74, 5, 'collection_selector_form', 0, 2, 0),
(116, 36, 71, 75, 5, 'orderedcollection_selector_form', 0, 2, 0),
(117, 38, 24, 101, 26, 'alternative-label', 0, 2, 0),
(118, 38, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(119, 38, 28, 102, 26, 'An-alternative-le-l-for-a-resource.', 0, 2, 0),
(120, 38, 44, 103, 26, 'Acronyms,-abbrevi-kos:hiddenLabel).', 0, 2, 0),
(121, 38, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(122, 38, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(123, 38, 85, 24, 5, 'http://www.w3.org-/rdf-schema#label', 0, 0, 0),
(124, 38, 90, 104, 26, 'The-range-of-skos-F-plain-literals.', 0, 2, 0),
(125, 38, 90, 105, 26, 'skos:prefLabel,-s-joint-properties.', 0, 2, 0),
(126, 38, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(127, 38, 95, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(128, 38, 95, 96, 5, 'http://www.w3.org-/core#hiddenLabel', 0, 0, 0),
(129, 38, 64, 97, 5, 'http://www.pmbser-tology#small_text', 0, 0, 0),
(130, 38, 18, 106, 5, 'altlabel', 0, 2, 0),
(131, 96, 24, 107, 26, 'hidden-label', 0, 2, 0),
(132, 96, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(133, 96, 28, 108, 26, 'A-lexical-label-f-earch-operations.', 0, 2, 0),
(134, 96, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(135, 96, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(136, 96, 85, 24, 5, 'http://www.w3.org-/rdf-schema#label', 0, 0, 0),
(137, 96, 90, 109, 26, 'The-range-of-skos-F-plain-literals.', 0, 2, 0),
(138, 96, 90, 105, 26, 'skos:prefLabel,-s-joint-properties.', 0, 2, 0),
(139, 96, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(140, 96, 95, 36, 5, 'http://www.w3.org-os/core#prefLabel', 0, 0, 0),
(141, 96, 95, 38, 5, 'http://www.w3.org-kos/core#altLabel', 0, 0, 0),
(142, 96, 64, 97, 5, 'http://www.pmbser-tology#small_text', 0, 0, 0),
(143, 96, 18, 110, 5, 'hiddenlabel', 0, 2, 0),
(144, 111, 24, 112, 26, 'notation', 0, 2, 0),
(145, 111, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(146, 111, 28, 113, 26, 'A-notation,-also--n-concept-scheme.', 0, 2, 0),
(147, 111, 42, 114, 26, 'By-convention,-sk-on-of-the-triple.', 0, 2, 0),
(148, 111, 3, 115, 5, 'http://www.w3.org-#DatatypeProperty', 0, 0, 0),
(149, 111, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(150, 111, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(151, 111, 64, 97, 5, 'http://www.pmbser-tology#small_text', 0, 0, 0),
(152, 111, 18, 112, 5, 'notation', 0, 2, 0),
(153, 116, 24, 117, 26, 'note', 0, 2, 0),
(154, 116, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(155, 116, 28, 118, 26, 'A-general-note,-for-any-purpose.', 0, 2, 0),
(156, 116, 42, 119, 26, 'This-property-may-cific-note-types.', 0, 2, 0),
(157, 116, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(158, 116, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(159, 116, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(160, 116, 64, 120, 5, 'http://www.pmbser-.fr/ontology#text', 0, 0, 0),
(161, 116, 18, 117, 5, 'note', 0, 2, 0),
(162, 121, 24, 122, 26, 'change-note', 0, 2, 0),
(163, 121, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(164, 121, 28, 123, 26, 'A-note-about-a-mo-ion-to-a-concept.', 0, 2, 0),
(165, 121, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(166, 121, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(167, 121, 85, 116, 5, 'http://www.w3.org-02/skos/core#note', 0, 0, 0),
(168, 121, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(169, 121, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(170, 121, 64, 120, 5, 'http://www.pmbser-.fr/ontology#text', 0, 0, 0),
(171, 121, 18, 124, 5, 'changenote', 0, 2, 0),
(172, 28, 24, 125, 26, 'definition', 0, 2, 0),
(173, 28, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(174, 28, 28, 126, 26, 'A-statement-or-fo-ing-of-a-concept.', 0, 2, 0),
(175, 28, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(176, 28, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(177, 28, 85, 116, 5, 'http://www.w3.org-02/skos/core#note', 0, 0, 0),
(178, 28, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(179, 28, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(180, 28, 64, 120, 5, 'http://www.pmbser-.fr/ontology#text', 0, 0, 0),
(181, 28, 18, 125, 5, 'definition', 0, 2, 0),
(182, 127, 24, 128, 26, 'editorial-note', 0, 2, 0),
(183, 127, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(184, 127, 28, 129, 26, 'A-note-for-an-edi-f-the-vocabulary.', 0, 2, 0),
(185, 127, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(186, 127, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(187, 127, 85, 116, 5, 'http://www.w3.org-02/skos/core#note', 0, 0, 0),
(188, 127, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(189, 127, 64, 120, 5, 'http://www.pmbser-.fr/ontology#text', 0, 0, 0),
(190, 127, 18, 130, 5, 'editorialnote', 0, 2, 0),
(191, 44, 24, 131, 26, 'example', 0, 2, 0),
(192, 44, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(193, 44, 28, 132, 26, 'An-example-of-the-use-of-a-concept.', 0, 2, 0),
(194, 44, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(195, 44, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(196, 44, 85, 116, 5, 'http://www.w3.org-02/skos/core#note', 0, 0, 0),
(197, 44, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(198, 44, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(199, 44, 64, 120, 5, 'http://www.pmbser-.fr/ontology#text', 0, 0, 0),
(200, 44, 18, 131, 5, 'example', 0, 2, 0),
(201, 133, 24, 134, 26, 'history-note', 0, 2, 0),
(202, 133, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(203, 133, 28, 135, 26, 'A-note-about-the--ing-of-a-concept.', 0, 2, 0),
(204, 133, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(205, 133, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(206, 133, 85, 116, 5, 'http://www.w3.org-02/skos/core#note', 0, 0, 0),
(207, 133, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(208, 133, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(209, 133, 64, 120, 5, 'http://www.pmbser-.fr/ontology#text', 0, 0, 0),
(210, 133, 18, 136, 5, 'historynote', 0, 2, 0),
(211, 42, 24, 137, 26, 'scope-note', 0, 2, 0),
(212, 42, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(213, 42, 28, 138, 26, 'A-note-that-helps-use-of-a-concept.', 0, 2, 0),
(214, 42, 3, 89, 5, 'http://www.w3.org-nnotationProperty', 0, 0, 0),
(215, 42, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(216, 42, 85, 116, 5, 'http://www.w3.org-02/skos/core#note', 0, 0, 0),
(217, 42, 63, 94, 5, 'http://www.w3.org-df-schema#Literal', 0, 0, 0),
(218, 42, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(219, 42, 64, 120, 5, 'http://www.pmbser-.fr/ontology#text', 0, 0, 0),
(220, 42, 18, 139, 5, 'scopenote', 0, 2, 0),
(221, 140, 24, 141, 26, 'is-in-semantic-relation-with', 0, 2, 0),
(222, 140, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(223, 140, 28, 142, 26, 'Links-a-concept-t-lated-by-meaning.', 0, 2, 0),
(224, 140, 42, 143, 26, 'This-property-sho-between-concepts.', 0, 2, 0),
(225, 140, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(226, 140, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(227, 140, 3, 144, 5, 'http://www.pmbser-AssertionProperty', 0, 0, 0),
(228, 140, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(229, 140, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(230, 140, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(231, 140, 18, 145, 5, 'semanticrelation', 0, 2, 0),
(232, 146, 24, 147, 26, 'has-broader', 0, 2, 0),
(233, 146, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(234, 146, 28, 148, 26, 'Relates-a-concept-neral-in-meaning.', 0, 2, 0),
(235, 146, 90, 149, 26, 'Broader-concepts--hierarchy-(tree).', 0, 2, 0),
(236, 146, 42, 150, 26, 'By-convention,-sk-eptual-resources.', 0, 2, 0),
(237, 146, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(238, 146, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(239, 146, 85, 151, 5, 'http://www.w3.org-broaderTransitive', 0, 0, 0),
(240, 146, 80, 152, 5, 'http://www.w3.org-kos/core#narrower', 0, 0, 0),
(241, 146, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(242, 146, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(243, 146, 95, 152, 5, 'http://www.w3.org-kos/core#narrower', 0, 0, 0),
(244, 146, 95, 153, 5, 'http://www.w3.org-skos/core#related', 0, 0, 0),
(245, 146, 95, 154, 5, 'http://www.w3.org-arrowerTransitive', 0, 0, 0),
(246, 146, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(247, 146, 18, 155, 5, 'broader', 0, 2, 0),
(248, 146, 67, 68, 5, 'http://www.pmbser-ontology#variable', 0, 0, 0),
(249, 146, 69, 156, 5, 'parent_id', 0, 2, 0),
(250, 146, 71, 72, 5, 'concept_selector_form', 0, 2, 0),
(251, 152, 24, 157, 26, 'has-narrower', 0, 2, 0),
(252, 152, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(253, 152, 28, 158, 26, 'Relates-a-concept-cific-in-meaning.', 0, 2, 0),
(254, 152, 42, 150, 26, 'By-convention,-sk-eptual-resources.', 0, 2, 0),
(255, 152, 90, 159, 26, 'Narrower-concepts-hierarchy-(tree).', 0, 2, 0),
(256, 152, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(257, 152, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(258, 152, 85, 154, 5, 'http://www.w3.org-arrowerTransitive', 0, 0, 0),
(259, 152, 80, 146, 5, 'http://www.w3.org-skos/core#broader', 0, 0, 0),
(260, 152, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(261, 152, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(262, 152, 95, 146, 5, 'http://www.w3.org-skos/core#broader', 0, 0, 0),
(263, 152, 95, 153, 5, 'http://www.w3.org-skos/core#related', 0, 0, 0),
(264, 152, 95, 151, 5, 'http://www.w3.org-broaderTransitive', 0, 0, 0),
(265, 152, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(266, 152, 18, 160, 5, 'narrower', 0, 2, 0),
(267, 153, 24, 161, 26, 'has-related', 0, 2, 0),
(268, 153, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(269, 153, 28, 162, 26, 'Relates-a-concept-tic-relationship.', 0, 2, 0),
(270, 153, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(271, 153, 3, 163, 5, 'http://www.w3.org-SymmetricProperty', 0, 0, 0),
(272, 153, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(273, 153, 85, 140, 5, 'http://www.w3.org-#semanticRelation', 0, 0, 0),
(274, 153, 90, 164, 26, 'skos:related-is-d-broaderTransitive', 0, 2, 0),
(275, 153, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(276, 153, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(277, 153, 95, 146, 5, 'http://www.w3.org-skos/core#broader', 0, 0, 0),
(278, 153, 95, 151, 5, 'http://www.w3.org-broaderTransitive', 0, 0, 0),
(279, 153, 95, 152, 5, 'http://www.w3.org-kos/core#narrower', 0, 0, 0),
(280, 153, 95, 154, 5, 'http://www.w3.org-arrowerTransitive', 0, 0, 0),
(281, 153, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(282, 153, 18, 165, 5, 'related', 0, 2, 0),
(283, 151, 24, 166, 26, 'has-broader-transitive', 0, 2, 0),
(284, 151, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(285, 151, 28, 167, 5, 'skos:broaderTrans--of-skos:broader.', 0, 2, 0),
(286, 151, 42, 168, 26, 'By-convention,-sk-arch-application.', 0, 2, 0),
(287, 151, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(288, 151, 3, 169, 5, 'http://www.w3.org-ransitiveProperty', 0, 0, 0),
(289, 151, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(290, 151, 3, 144, 5, 'http://www.pmbser-AssertionProperty', 0, 0, 0),
(291, 151, 85, 140, 5, 'http://www.w3.org-#semanticRelation', 0, 0, 0),
(292, 151, 80, 154, 5, 'http://www.w3.org-arrowerTransitive', 0, 0, 0),
(293, 151, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(294, 151, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(295, 151, 95, 152, 5, 'http://www.w3.org-kos/core#narrower', 0, 0, 0),
(296, 151, 95, 153, 5, 'http://www.w3.org-skos/core#related', 0, 0, 0),
(297, 151, 95, 154, 5, 'http://www.w3.org-arrowerTransitive', 0, 0, 0),
(298, 151, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(299, 151, 18, 170, 5, 'broadertransitive', 0, 2, 0),
(300, 154, 24, 171, 26, 'has-narrower-transitive', 0, 2, 0),
(301, 154, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(302, 154, 28, 172, 5, 'skos:narrowerTran-of-skos:narrower.', 0, 2, 0),
(303, 154, 42, 173, 26, 'By-convention,-sk-arch-application.', 0, 2, 0),
(304, 154, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(305, 154, 3, 169, 5, 'http://www.w3.org-ransitiveProperty', 0, 0, 0),
(306, 154, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(307, 154, 3, 144, 5, 'http://www.pmbser-AssertionProperty', 0, 0, 0),
(308, 154, 85, 140, 5, 'http://www.w3.org-#semanticRelation', 0, 0, 0),
(309, 154, 80, 151, 5, 'http://www.w3.org-broaderTransitive', 0, 0, 0),
(310, 154, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(311, 154, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(312, 154, 95, 146, 5, 'http://www.w3.org-skos/core#broader', 0, 0, 0),
(313, 154, 95, 153, 5, 'http://www.w3.org-skos/core#related', 0, 0, 0),
(314, 154, 95, 151, 5, 'http://www.w3.org-broaderTransitive', 0, 0, 0),
(315, 154, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(316, 154, 18, 174, 5, 'narrowertransitive', 0, 2, 0),
(317, 175, 24, 176, 26, 'has-member', 0, 2, 0),
(318, 175, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(319, 175, 28, 177, 26, 'Relates-a-collect-e-of-its-members.', 0, 2, 0),
(320, 175, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(321, 175, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(322, 175, 79, 23, 5, 'http://www.w3.org-s/core#Collection', 0, 0, 0),
(323, 175, 63, 178, 5, '_:b3760457392__arc92ccb1', 0, 1, 0),
(324, 175, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(325, 175, 18, 179, 5, 'member', 0, 2, 0),
(326, 178, 3, 30, 5, 'http://www.w3.org/2002/07/owl#Class', 1, 0, 0),
(327, 178, 180, 181, 5, '_:b2331651819__arc92ccb2', 1, 1, 0),
(328, 181, 182, 20, 5, 'http://www.w3.org-skos/core#Concept', 1, 0, 0),
(329, 181, 183, 184, 5, '_:b1717355975__arc92ccb3', 1, 1, 0),
(330, 184, 182, 23, 5, 'http://www.w3.org-s/core#Collection', 1, 0, 0),
(331, 184, 183, 185, 5, 'http://www.w3.org-rdf-syntax-ns#nil', 1, 0, 0),
(332, 186, 24, 187, 26, 'has-member-list', 0, 2, 0),
(333, 186, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(334, 186, 28, 188, 26, 'Relates-an-ordere-ning-its-members.', 0, 2, 0),
(335, 186, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(336, 186, 3, 189, 5, 'http://www.w3.org-unctionalProperty', 0, 0, 0),
(337, 186, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(338, 186, 79, 52, 5, 'http://www.w3.org-OrderedCollection', 0, 0, 0),
(339, 186, 63, 190, 5, 'http://www.w3.org-df-syntax-ns#List', 0, 0, 0),
(340, 186, 90, 191, 26, 'For-any-resource,-:member-property.', 0, 2, 0),
(341, 186, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(342, 186, 18, 192, 5, 'memberlist', 0, 2, 0),
(343, 193, 24, 194, 26, 'is-in-mapping-relation-with', 0, 2, 0),
(344, 193, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(345, 193, 28, 195, 26, 'Relates-two-conce-mparable-meanings', 0, 2, 0),
(346, 193, 90, 196, 26, 'These-concept-map-of-best-practice.', 0, 2, 0),
(347, 193, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(348, 193, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(349, 193, 3, 144, 5, 'http://www.pmbser-AssertionProperty', 0, 0, 0),
(350, 193, 85, 140, 5, 'http://www.w3.org-#semanticRelation', 0, 0, 0),
(351, 193, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(352, 193, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(353, 193, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(354, 193, 18, 197, 5, 'mappingrelation', 0, 2, 0),
(355, 198, 24, 199, 26, 'has-broader-match', 0, 2, 0),
(356, 198, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(357, 198, 28, 200, 26, 'skos:broadMatch-i--concept-schemes.', 0, 2, 0),
(358, 198, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(359, 198, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(360, 198, 85, 193, 5, 'http://www.w3.org-e#mappingRelation', 0, 0, 0),
(361, 198, 85, 146, 5, 'http://www.w3.org-skos/core#broader', 0, 0, 0),
(362, 198, 80, 201, 5, 'http://www.w3.org-/core#narrowMatch', 0, 0, 0),
(363, 198, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(364, 198, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(365, 198, 95, 202, 5, 'http://www.w3.org-s/core#exactMatch', 0, 0, 0),
(366, 198, 95, 203, 5, 'http://www.w3.org-core#relatedMatch', 0, 0, 0),
(367, 198, 95, 201, 5, 'http://www.w3.org-/core#narrowMatch', 0, 0, 0),
(368, 198, 95, 204, 5, 'http://www.w3.org-s/core#closeMatch', 0, 0, 0),
(369, 198, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(370, 198, 18, 205, 5, 'broadmatch', 0, 2, 0),
(371, 201, 24, 206, 26, 'has-narrower-match', 0, 2, 0),
(372, 201, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(373, 201, 28, 207, 26, 'skos:narrowMatch---concept-schemes.', 0, 2, 0),
(374, 201, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(375, 201, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(376, 201, 85, 193, 5, 'http://www.w3.org-e#mappingRelation', 0, 0, 0),
(377, 201, 85, 152, 5, 'http://www.w3.org-kos/core#narrower', 0, 0, 0),
(378, 201, 80, 198, 5, 'http://www.w3.org-s/core#broadMatch', 0, 0, 0),
(379, 201, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(380, 201, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(381, 201, 95, 202, 5, 'http://www.w3.org-s/core#exactMatch', 0, 0, 0),
(382, 201, 95, 203, 5, 'http://www.w3.org-core#relatedMatch', 0, 0, 0),
(383, 201, 95, 198, 5, 'http://www.w3.org-s/core#broadMatch', 0, 0, 0),
(384, 201, 95, 204, 5, 'http://www.w3.org-s/core#closeMatch', 0, 0, 0),
(385, 201, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(386, 201, 18, 208, 5, 'narrowmatch', 0, 2, 0),
(387, 203, 24, 209, 26, 'has-related-match', 0, 2, 0),
(388, 203, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(389, 203, 28, 210, 26, 'skos:relatedMatch--concept-schemes.', 0, 2, 0),
(390, 203, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(391, 203, 3, 163, 5, 'http://www.w3.org-SymmetricProperty', 0, 0, 0),
(392, 203, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(393, 203, 85, 193, 5, 'http://www.w3.org-e#mappingRelation', 0, 0, 0),
(394, 203, 85, 153, 5, 'http://www.w3.org-skos/core#related', 0, 0, 0),
(395, 203, 80, 203, 5, 'http://www.w3.org-core#relatedMatch', 0, 0, 0),
(396, 203, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(397, 203, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(398, 203, 95, 202, 5, 'http://www.w3.org-s/core#exactMatch', 0, 0, 0),
(399, 203, 95, 201, 5, 'http://www.w3.org-/core#narrowMatch', 0, 0, 0),
(400, 203, 95, 198, 5, 'http://www.w3.org-s/core#broadMatch', 0, 0, 0),
(401, 203, 95, 204, 5, 'http://www.w3.org-s/core#closeMatch', 0, 0, 0),
(402, 203, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(403, 203, 18, 211, 5, 'relatedmatch', 0, 2, 0),
(404, 202, 24, 212, 26, 'has-exact-match', 0, 2, 0),
(405, 202, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(406, 202, 28, 213, 26, 'skos:exactMatch-i--skos:closeMatch.', 0, 2, 0),
(407, 202, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(408, 202, 3, 163, 5, 'http://www.w3.org-SymmetricProperty', 0, 0, 0),
(409, 202, 3, 169, 5, 'http://www.w3.org-ransitiveProperty', 0, 0, 0),
(410, 202, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(411, 202, 85, 204, 5, 'http://www.w3.org-s/core#closeMatch', 0, 0, 0),
(412, 202, 80, 202, 5, 'http://www.w3.org-s/core#exactMatch', 0, 0, 0),
(413, 202, 90, 214, 26, 'skos:exactMatch-i-kos:relatedMatch.', 0, 2, 0),
(414, 202, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(415, 202, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(416, 202, 95, 203, 5, 'http://www.w3.org-core#relatedMatch', 0, 0, 0),
(417, 202, 95, 201, 5, 'http://www.w3.org-/core#narrowMatch', 0, 0, 0),
(418, 202, 95, 198, 5, 'http://www.w3.org-s/core#broadMatch', 0, 0, 0),
(419, 202, 95, 204, 5, 'http://www.w3.org-s/core#closeMatch', 0, 0, 0),
(420, 202, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(421, 202, 18, 215, 5, 'exactmatch', 0, 2, 0),
(422, 204, 24, 216, 26, 'has-close-match', 0, 2, 0),
(423, 204, 27, 2, 5, 'http://www.w3.org/2004/02/skos/core', 0, 0, 0),
(424, 204, 28, 217, 26, 'skos:closeMatch-i-nsitive-property.', 0, 2, 0),
(425, 204, 3, 61, 5, 'http://www.w3.org-wl#ObjectProperty', 0, 0, 0),
(426, 204, 3, 163, 5, 'http://www.w3.org-SymmetricProperty', 0, 0, 0),
(427, 204, 3, 62, 5, 'http://www.w3.org-yntax-ns#Property', 0, 0, 0),
(428, 204, 85, 193, 5, 'http://www.w3.org-e#mappingRelation', 0, 0, 0),
(429, 204, 63, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(430, 204, 79, 20, 5, 'http://www.w3.org-skos/core#Concept', 0, 0, 0),
(431, 204, 95, 203, 5, 'http://www.w3.org-core#relatedMatch', 0, 0, 0),
(432, 204, 95, 201, 5, 'http://www.w3.org-/core#narrowMatch', 0, 0, 0),
(433, 204, 95, 198, 5, 'http://www.w3.org-s/core#broadMatch', 0, 0, 0),
(434, 204, 95, 202, 5, 'http://www.w3.org-s/core#exactMatch', 0, 0, 0),
(435, 204, 64, 65, 5, 'http://www.pmbser-resource_selector', 0, 0, 0),
(436, 204, 18, 218, 5, 'closematch', 0, 2, 0),
(437, 34, 3, 219, 5, 'http://www.w3.org-7/owl#Restriction', 1, 0, 0),
(438, 34, 220, 36, 5, 'http://www.w3.org-os/core#prefLabel', 1, 0, 0),
(439, 34, 221, 222, 223, '+000000000000000001.000000000000000', 1, 2, 0),
(440, 34, 224, 222, 223, '+000000000000000001.000000000000000', 1, 2, 0),
(441, 33, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(442, 33, 220, 36, 5, 'http://www.w3.org-os/core#prefLabel', 1, 0, 0),
(443, 33, 99, 100, 5, '+000000000000000130.000000000000000', 1, 2, 0),
(444, 33, 226, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(445, 33, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(446, 33, 180, 228, 5, '_:b3728233012_eptindex_1', 1, 1, 0),
(447, 228, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(448, 228, 220, 38, 5, 'http://www.w3.org-kos/core#altLabel', 1, 0, 0),
(449, 228, 99, 100, 5, '+000000000000000130.000000000000000', 1, 2, 0),
(450, 228, 226, 229, 5, '+000000000000000002.000000000000000', 1, 2, 0),
(451, 228, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(452, 228, 180, 230, 5, '_:b1194394510_eptindex_2', 1, 1, 0),
(453, 230, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(454, 230, 220, 96, 5, 'http://www.w3.org-/core#hiddenLabel', 1, 0, 0),
(455, 230, 99, 100, 5, '+000000000000000130.000000000000000', 1, 2, 0),
(456, 230, 226, 231, 5, '+000000000000000003.000000000000000', 1, 2, 0),
(457, 230, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(458, 230, 180, 232, 5, '_:b808858392_eptindex_3', 1, 1, 0),
(459, 232, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(460, 232, 220, 57, 5, 'http://www.w3.org-kos/core#inScheme', 1, 0, 0),
(461, 232, 233, 36, 5, 'http://www.w3.org-os/core#prefLabel', 1, 0, 0),
(462, 232, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(463, 232, 226, 235, 5, '+000000000000000004.000000000000000', 1, 2, 0),
(464, 232, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(465, 232, 180, 236, 5, '_:b1079822231_eptindex_6', 1, 1, 0),
(466, 236, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(467, 236, 237, 111, 5, 'http://www.w3.org-kos/core#notation', 1, 0, 0),
(468, 236, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(469, 236, 226, 238, 5, '+000000000000000007.000000000000000', 1, 2, 0),
(470, 236, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(471, 236, 180, 239, 5, '_:b928773889_eptindex_7', 1, 1, 0),
(472, 239, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(473, 239, 220, 116, 5, 'http://www.w3.org-02/skos/core#note', 1, 0, 0),
(474, 239, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(475, 239, 226, 240, 5, '+000000000000000008.000000000000000', 1, 2, 0),
(476, 239, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(477, 239, 180, 241, 5, '_:b2816797328_eptindex_8', 1, 1, 0),
(478, 241, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(479, 241, 220, 121, 5, 'http://www.w3.org-s/core#changeNote', 1, 0, 0),
(480, 241, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(481, 241, 226, 242, 5, '+000000000000000009.000000000000000', 1, 2, 0),
(482, 241, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(483, 241, 180, 243, 5, '_:b3504593414_eptindex_9', 1, 1, 0),
(484, 243, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(485, 243, 220, 28, 5, 'http://www.w3.org-s/core#definition', 1, 0, 0),
(486, 243, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(487, 243, 226, 244, 5, '+000000000000000010.000000000000000', 1, 2, 0),
(488, 243, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(489, 243, 180, 245, 5, '_:b3585151942_ptindex_10', 1, 1, 0),
(490, 245, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(491, 245, 220, 127, 5, 'http://www.w3.org-ore#editorialNote', 1, 0, 0),
(492, 245, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(493, 245, 226, 246, 5, '+000000000000000011.000000000000000', 1, 2, 0),
(494, 245, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(495, 245, 180, 247, 5, '_:b2729845584_ptindex_11', 1, 1, 0),
(496, 247, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(497, 247, 220, 44, 5, 'http://www.w3.org-skos/core#example', 1, 0, 0),
(498, 247, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(499, 247, 226, 248, 5, '+000000000000000012.000000000000000', 1, 2, 0),
(500, 247, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(501, 247, 180, 249, 5, '_:b1002402538_ptindex_12', 1, 1, 0),
(502, 249, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(503, 249, 220, 133, 5, 'http://www.w3.org-/core#historyNote', 1, 0, 0),
(504, 249, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(505, 249, 226, 250, 5, '+000000000000000013.000000000000000', 1, 2, 0),
(506, 249, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(507, 249, 180, 251, 5, '_:b1287144060_ptindex_13', 1, 1, 0),
(508, 251, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(509, 251, 220, 42, 5, 'http://www.w3.org-os/core#scopeNote', 1, 0, 0),
(510, 251, 99, 234, 5, '+000000000000000030.000000000000000', 1, 2, 0),
(511, 251, 226, 252, 5, '+000000000000000014.000000000000000', 1, 2, 0),
(512, 251, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(513, 46, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(514, 46, 220, 36, 5, 'http://www.w3.org-os/core#prefLabel', 1, 0, 0),
(515, 46, 226, 253, 5, '+000000000000000100.000000000000000', 1, 2, 0),
(516, 46, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(517, 46, 99, 100, 5, '+000000000000000130.000000000000000', 1, 2, 0),
(518, 46, 180, 254, 5, '_:b1112853654_emeindex_1', 1, 1, 0),
(519, 254, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(520, 254, 220, 38, 5, 'http://www.w3.org-kos/core#altLabel', 1, 0, 0),
(521, 254, 226, 255, 5, '+000000000000000101.000000000000000', 1, 2, 0),
(522, 254, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(523, 254, 99, 100, 5, '+000000000000000130.000000000000000', 1, 2, 0),
(524, 254, 180, 256, 5, '_:b3680345388_emeindex_2', 1, 1, 0),
(525, 256, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(526, 256, 220, 96, 5, 'http://www.w3.org-/core#hiddenLabel', 1, 0, 0),
(527, 256, 226, 257, 5, '+000000000000000102.000000000000000', 1, 2, 0),
(528, 256, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(529, 256, 99, 100, 5, '+000000000000000130.000000000000000', 1, 2, 0),
(530, 256, 180, 258, 5, '_:b2891623866_emeindex_3', 1, 1, 0),
(531, 258, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(532, 258, 220, 76, 5, 'http://www.w3.org-ore#hasTopConcept', 1, 0, 0),
(533, 258, 233, 36, 5, 'http://www.w3.org-os/core#prefLabel', 1, 0, 0),
(534, 258, 99, 259, 5, '+000000000000000060.000000000000000', 1, 2, 0),
(535, 258, 226, 260, 5, '+000000000000000103.000000000000000', 1, 2, 0),
(536, 258, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0),
(537, 258, 180, 261, 5, '_:b324127774_meindex_4-', 1, 1, 0),
(538, 262, 3, 225, 5, 'http://www.pmbser-tology#indexation', 1, 0, 0),
(539, 262, 220, 57, 5, 'http://www.w3.org-kos/core#inScheme', 1, 0, 0),
(540, 262, 233, 36, 5, 'http://www.w3.org-os/core#prefLabel', 1, 0, 0),
(541, 262, 99, 259, 5, '+000000000000000060.000000000000000', 1, 2, 0),
(542, 262, 226, 263, 5, '+000000000000000104.000000000000000', 1, 2, 0),
(543, 262, 227, 222, 5, '+000000000000000001.000000000000000', 1, 2, 0);

-- --------------------------------------------------------

--
-- Structure de la table `onto_files`
--

CREATE TABLE IF NOT EXISTS `onto_files` (
  `id_onto_file` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `onto_file_title` varchar(255) NOT NULL DEFAULT '',
  `onto_file_description` text NOT NULL,
  `onto_file_filename` varchar(255) NOT NULL DEFAULT '',
  `onto_file_mimetype` varchar(100) NOT NULL DEFAULT '',
  `onto_file_filesize` int(11) NOT NULL DEFAULT 0,
  `onto_file_vignette` mediumblob NOT NULL,
  `onto_file_url` text NOT NULL,
  `onto_file_path` varchar(255) NOT NULL DEFAULT '',
  `onto_file_create_date` date NOT NULL DEFAULT '0000-00-00',
  `onto_file_num_storage` int(11) NOT NULL DEFAULT 0,
  `onto_file_type_object` varchar(255) NOT NULL DEFAULT '',
  `onto_file_num_object` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_onto_file`),
  KEY `i_of_onto_file_title` (`onto_file_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `onto_uri`
--

CREATE TABLE IF NOT EXISTS `onto_uri` (
  `uri_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uri_id`),
  UNIQUE KEY `uri` (`uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `opac_filters`
--

CREATE TABLE IF NOT EXISTS `opac_filters` (
  `opac_filter_view_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `opac_filter_path` varchar(20) NOT NULL DEFAULT '',
  `opac_filter_param` text NOT NULL,
  PRIMARY KEY (`opac_filter_view_num`,`opac_filter_path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `opac_liste_lecture`
--

CREATE TABLE IF NOT EXISTS `opac_liste_lecture` (
  `id_liste` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_liste` varchar(255) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `public` int(1) NOT NULL DEFAULT 0,
  `num_empr` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `read_only` int(1) NOT NULL DEFAULT 0,
  `confidential` int(1) NOT NULL DEFAULT 0,
  `tag` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_liste`),
  KEY `i_num_empr` (`num_empr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `opac_liste_lecture_notices`
--

CREATE TABLE IF NOT EXISTS `opac_liste_lecture_notices` (
  `opac_liste_lecture_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `opac_liste_lecture_notice_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `opac_liste_lecture_create_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`opac_liste_lecture_num`,`opac_liste_lecture_notice_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `opac_sessions`
--

CREATE TABLE IF NOT EXISTS `opac_sessions` (
  `empr_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `session` mediumblob DEFAULT NULL,
  `date_rec` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`empr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `opac_views`
--

CREATE TABLE IF NOT EXISTS `opac_views` (
  `opac_view_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `opac_view_name` varchar(255) NOT NULL DEFAULT '',
  `opac_view_query` text NOT NULL,
  `opac_view_human_query` text NOT NULL,
  `opac_view_param` text NOT NULL,
  `opac_view_visible` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `opac_view_comment` text NOT NULL,
  `opac_view_last_gen` datetime DEFAULT NULL,
  `opac_view_ttl` int(11) NOT NULL DEFAULT 86400,
  PRIMARY KEY (`opac_view_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `opac_views_empr`
--

CREATE TABLE IF NOT EXISTS `opac_views_empr` (
  `emprview_view_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `emprview_empr_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `emprview_default` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`emprview_view_num`,`emprview_empr_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `openid_association`
--

CREATE TABLE IF NOT EXISTS `openid_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idp_endpoint_uri` text NOT NULL,
  `session_type` varchar(30) NOT NULL,
  `assoc_handle` text NOT NULL,
  `assoc_type` text NOT NULL,
  `expires_in` bigint(20) NOT NULL,
  `mac_key` text NOT NULL,
  `created` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `orga_membre`
--

CREATE TABLE IF NOT EXISTS `orga_membre` (
  `id_membre` int(10) NOT NULL AUTO_INCREMENT,
  `id_service` int(10) NOT NULL DEFAULT -1,
  `nom_membre` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `prenom_membre` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `fonction_membre` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `telephone_membre` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `adresse_membre` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `cp_membre` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `ville_membre` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `photo_membre` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `actif_membre` enum('O','N') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'O',
  `id_organigramme` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_membre`),
  KEY `id_service` (`id_service`),
  KEY `id_organigramme` (`id_organigramme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `orga_organigramme`
--

CREATE TABLE IF NOT EXISTS `orga_organigramme` (
  `id_organigramme` int(10) NOT NULL AUTO_INCREMENT,
  `title_organigramme` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `id_membre_dg` int(10) NOT NULL DEFAULT 0,
  `id_service_dg` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_organigramme`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `orga_organigramme`
--

INSERT INTO `orga_organigramme` VALUES
(1, 'Organigramme', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `orga_phporg_config`
--

CREATE TABLE IF NOT EXISTS `orga_phporg_config` (
  `phporg_version` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `orga_phporg_config`
--

INSERT INTO `orga_phporg_config` VALUES
('1.0.6');

-- --------------------------------------------------------

--
-- Structure de la table `orga_service`
--

CREATE TABLE IF NOT EXISTS `orga_service` (
  `id_service` int(10) NOT NULL AUTO_INCREMENT,
  `nom_service` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `lib_service` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `id_responsable` int(10) NOT NULL DEFAULT -1,
  `id_organigramme` int(10) NOT NULL DEFAULT 1,
  `id_service_parent` int(10) NOT NULL DEFAULT -1,
  `color_service` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '#000000',
  `bgcolor_service` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '#9D9DCE',
  `opened_service` tinyint(1) NOT NULL DEFAULT 1,
  `display_vertical_service` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_service`),
  KEY `id_responsable` (`id_responsable`),
  KEY `id_organigramme` (`id_organigramme`),
  KEY `id_service_parent` (`id_service_parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `origine_notice`
--

CREATE TABLE IF NOT EXISTS `origine_notice` (
  `orinot_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `orinot_nom` varchar(255) NOT NULL DEFAULT '',
  `orinot_pays` varchar(255) NOT NULL DEFAULT 'FR',
  `orinot_diffusion` int(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`orinot_id`),
  KEY `orinot_nom` (`orinot_nom`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `origine_notice`
--

INSERT INTO `origine_notice` VALUES
(3, 'Catalogage interne', 'FR', 1),
(2, 'BnF', 'FR', 1),
(1, 'INTERNE', 'FR', 1),
(4, 'FR-751131015', 'FR', 1);

-- --------------------------------------------------------

--
-- Structure de la table `origin_authorities`
--

CREATE TABLE IF NOT EXISTS `origin_authorities` (
  `id_origin_authorities` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `origin_authorities_name` varchar(255) NOT NULL DEFAULT '',
  `origin_authorities_country` varchar(10) NOT NULL DEFAULT '',
  `origin_authorities_diffusible` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_origin_authorities`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `origin_authorities`
--

INSERT INTO `origin_authorities` VALUES
(1, 'Catalogue Interne', 'FR', 1),
(2, 'BnF', 'FR', 1);

-- --------------------------------------------------------

--
-- Structure de la table `ouvertures`
--

CREATE TABLE IF NOT EXISTS `ouvertures` (
  `date_ouverture` date NOT NULL DEFAULT '0000-00-00',
  `ouvert` int(1) NOT NULL DEFAULT 1,
  `commentaire` varchar(255) NOT NULL DEFAULT '',
  `num_location` int(3) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`date_ouverture`,`num_location`),
  KEY `i_ouvert_num_location_date_ouverture` (`ouvert`,`num_location`,`date_ouverture`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE IF NOT EXISTS `paiements` (
  `id_paiement` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id_paiement`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE IF NOT EXISTS `parametres` (
  `id_param` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_param` varchar(20) DEFAULT NULL,
  `sstype_param` varchar(255) DEFAULT NULL,
  `valeur_param` text DEFAULT NULL,
  `comment_param` longtext DEFAULT NULL,
  `section_param` varchar(255) NOT NULL DEFAULT '',
  `gestion` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_param`),
  UNIQUE KEY `typ_sstyp` (`type_param`,`sstype_param`)
) ENGINE=MyISAM AUTO_INCREMENT=1116 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `parametres`
--

INSERT INTO `parametres` VALUES
(1, 'pmb', 'bdd_version', 'v5.32', 'Version de noyau de la base de données, à ne changer qu\'en version inférieure si un paramètre était mal passé et relancer la mise à jour. En général, contactez plutôt la mailing liste pmb.user@sigb.net', '', 0),
(2, 'z3950', 'accessible', '1', 'Z3950 accessible ?\r\n 0 : non, menu inaccessible\r\n 1 : Oui, la librairie PHP_YAZ est activée, la recherche z3950 est possible', '', 0),
(3, 'pmb', 'nb_lastautorities', '10', 'Nombre de dernières autoritées affichées en gestion d\'autorités', '', 0),
(4, 'pdflettreretard', '1before_list', 'Sauf erreur de notre part, vous avez toujours en votre possession le ou les ouvrage(s) suivant(s) dont la durée de prêt est aujourd\'hui dépassée :', 'Texte apparaissant avant la liste des ouvrages en retard dans le courrier de relance de retard', '', 0),
(5, 'pdflettreretard', '1after_list', 'Nous vous remercions de prendre rapidement contact par téléphone au $biblio_phone ou par mail à $biblio_email pour étudier la possibilité de prolonger ces prêts ou de rapporter les ouvrages concernés.', 'Texte apparaissant après la liste des ouvrages en retard dans le courrier', '', 0),
(6, 'pdflettreretard', '1fdp', 'Le responsable.', 'Signataire de la lettre.', '', 0),
(7, 'pdflettreretard', '1madame_monsieur', 'Madame, Monsieur,', 'Entête de la lettre', '', 0),
(8, 'pdflettreretard', '1nb_par_page', '7', 'Nombre d\'ouvrages en retard imprimé sur les pages suivantes.', '', 0),
(9, 'pdflettreretard', '1nb_1ere_page', '4', 'Nombre d\'ouvrages en retard imprimé sur la première page', '', 0),
(10, 'pdflettreretard', '1taille_bloc_expl', '16', 'Taille d\'un bloc (2 lignes) d\'ouvrage en retard. Le début de chaque ouvrage en retard sera espacé de cette valeur sur la page', '', 0),
(11, 'pdflettreretard', '1debut_expl_1er_page', '160', 'Début de la liste des exemplaires sur la première page, en mm depuis le bord supérieur de la page. Doit être règlé en fonction du texte qui précède la liste des ouvrages, lequel peut être plus ou moins long.', '', 0),
(12, 'pdflettreretard', '1debut_expl_page', '15', 'Début de la liste des exemplaires sur les pages suivantes, en mm depuis le bord supérieur de la page.', '', 0),
(13, 'pdflettreretard', '1limite_after_list', '270', 'Position limite en bas de page. Si un élément imprimé tente de dépasser cette limite, il sera imprimé sur la page suivante.', '', 0),
(14, 'pdflettreretard', '1marge_page_gauche', '10', 'Marge de gauche en mm', '', 0),
(15, 'pdflettreretard', '1marge_page_droite', '10', 'Marge de droite en mm', '', 0),
(16, 'pdflettreretard', '1largeur_page', '210', 'Largeur de la page en mm', '', 0),
(17, 'pdflettreretard', '1hauteur_page', '297', 'Hauteur de la page en mm', '', 0),
(18, 'pdflettreretard', '1format_page', 'P', 'Format de la page : \r\n P : Portrait\r\n L : Landscape = paysage', '', 0),
(19, 'pdfcartelecteur', 'pos_h', '20', 'Position horizontale en mm à partir du bord gauche de la page', '', 0),
(20, 'pdfcartelecteur', 'pos_v', '20', 'Position verticale en mm à partir du bord supérieur de la page', '', 0),
(21, 'pdfcartelecteur', 'biblio_name', '$biblio_name', 'Nom de la bibliothèque ou du centre de ressources imprimé sur la carte de lecteur. Mettre $biblio_name pour reprendre le nom spécifié en localisation d\'exemplaire ou bien mettre autre chose.', '', 0),
(22, 'pdfcartelecteur', 'largeur_nom', '80', 'Largeur accordée à l\'impression du nom du lecteur en mm', '', 0),
(23, 'pdfcartelecteur', 'valabledu', 'Valable du', '\'Valable du\' dans \"VALABLE DU ##/##/#### au ##/##/####\"', '', 0),
(24, 'pdfcartelecteur', 'valableau', 'au', '\'au\' dans \"valable du ##/##/#### AU ##/##/####\"', '', 0),
(25, 'pdfcartelecteur', 'carteno', 'Carte N° :', 'Mention précédant le numéro de la carte', '', 0),
(26, 'sauvegarde', 'cle_crypt1', '9b4a840d790eadc71b9064c9a843719b', '', '', 0),
(27, 'sauvegarde', 'cle_crypt2', '51580d4fd5f1ad2d981c91ddb04095ec', '', '', 0),
(28, 'pmb', 'resa_dispo', '1', 'Réservation de documents disponibles possible ?\r\n 0 : Non\r\n 1 : Oui', '', 0),
(29, 'mailretard', '1objet', '$biblio_name : documents en retard', 'Objet du mail de relance de retard', '', 0),
(30, 'mailretard', '1before_list', 'Sauf erreur de notre part, vous avez toujours en votre possession le ou les ouvrage(s) suivant(s) dont la durée de prêt est aujourd\'hui dépassée :', 'Texte apparaissant avant la liste des ouvrages en retard dans le mail de relance de retard', '', 0),
(31, 'mailretard', '1after_list', 'Nous vous remercions de prendre rapidement contact par téléphone au $biblio_phone ou par mail à $biblio_email pour étudier la possibilité de prolonger ces prêts ou de rapporter les ouvrages concernés.', 'Texte apparaissant après la liste des ouvrages en retard dans le mail', '', 0),
(32, 'mailretard', '1madame_monsieur', 'Madame, Monsieur', 'Entête du mail', '', 0),
(33, 'mailretard', '1fdp', 'Le responsable.', 'Signataire du mail de relance de retard', '', 0),
(34, 'pmb', 'serial_link_article', '0', 'Préremplissage du lien des dépouillements avec le lien de la notice mère en catalogage des périodiques ?\r\n 0 : Non\r\n 1 : Oui', '', 0),
(35, 'pmb', 'num_carte_auto', '1', 'Numéro de carte de lecteur automatique ?\n 0: Non (si utilisation de cartes pré-imprimées)\n 1: Oui, entièrement numérique\n 2,a,b,c: Oui, avec préfixe: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)\n 3,fonction: fonction de génération spécifique dans fichier nommé de la même façon, à placer dans pmb/circ/empr', '', 0),
(36, 'opac', 'modules_search_title', '1', 'Recherche simple dans les titres :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(37, 'opac', 'modules_search_author', '1', 'Recherche simple dans les auteurs :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(38, 'opac', 'modules_search_publisher', '1', 'Recherche simple dans les éditeurs :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(39, 'opac', 'modules_search_collection', '0', 'Recherche simple dans les collections :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(40, 'opac', 'modules_search_subcollection', '0', 'Recherche simple dans les sous-collections :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(41, 'opac', 'modules_search_category', '1', 'Recherche simple dans les catégories :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(42, 'opac', 'modules_search_keywords', '1', 'Recherche simple dans les indexations libres (mots-clés) :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(43, 'opac', 'modules_search_abstract', '1', 'Recherche simple dans le champ résumé :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(44, 'opac', 'modules_search_content', '0', 'Recherche simple dans les notes de contenu:\r\n 0 : interdite\r\n 1 : autorisée\r\n 2 : autorisée et validée par défaut\r\nINUTILISE POUR L\'INSTANT', 'c_recherche', 0),
(45, 'opac', 'categories_categ_path_sep', '>', 'Séparateur pour les catégories', 'i_categories', 0),
(46, 'opac', 'categories_columns', '3', 'Nombre de colonnes du sommaire général des catégories', 'i_categories', 0),
(47, 'opac', 'categories_categ_rec_per_page', '6', 'Nombre de notices à afficher par page dans l\'exploration des catégories', 'i_categories', 0),
(48, 'opac', 'categories_categ_sort_records', 'index_serie, tnvol, index_sew', 'Explorateur de catégories : mode de tri des notices :\r\n index_serie, tnvol, index_sew > par titre de série, numéro dans la série et index des titres\r\n rand() : aléatoire', 'i_categories', 1),
(49, 'opac', 'search_results_first_level', '4', 'Nombre de résultats affichés sur la première page', 'z_unused', 0),
(50, 'opac', 'search_results_per_page', '10', 'Nombre de résultats affichés sur les pages suivantes', 'd_aff_recherche', 0),
(832, 'demandes', 'init_workflow', '1', 'Initialisation du workflow de la demande.\n 0 : Validation avant tout\n 1 : Validation avant tout et attribution au validateur\n 2 : Attribution avant tout', '', 0),
(52, 'opac', 'categories_sub_display', '3', 'Nombre de sous-categories sur la première page', 'i_categories', 0),
(53, 'opac', 'categories_sub_mode', 'libelle_categorie', 'Mode affichage des sous-categories : \r\n rand() > aléatoire\r\n libelle_categorie > ordre alpha', 'i_categories', 0),
(55, 'opac', 'default_lang', 'fr_FR', 'Langue de l\'opac : fr_FR ou en_US ou es_ES ou ar', 'a_general', 0),
(56, 'opac', 'show_categ_browser', '1', 'Affichage des catégories en page d\'accueil OPAC:\n0: Non\n1: Oui\n1 3,1: Oui, avec thésaurus id 3 puis 1 (préciser les thésaurus à afficher et l\'ordre)', 'f_modules', 0),
(57, 'opac', 'show_book_pics', '1', 'Afficher les vignettes de livres dans les fiches ouvrages :\r\n 0 : Non\r\n 1 : Oui', 'e_aff_notice', 0),
(58, 'opac', 'resa', '1', 'Réservations possibles par l\'OPAC 1: oui  ou 0: non', 'a_general', 0),
(59, 'opac', 'resa_dispo', '1', 'Réservations possibles de documents disponibles par l\'OPAC \r\n 1: oui \r\n 0: non', 'a_general', 0),
(60, 'opac', 'show_meteo', '0', 'Affichage de la météo dans l\'OPAC 1: oui  ou 0: non', 'f_modules', 0),
(61, 'opac', 'duration_session_auth', '1200', 'Durée de la session lecteur dans l\'OPAC en secondes', 'a_general', 0),
(62, 'pmb', 'relance_adhesion', '31', 'Nombre de jours avant expiration adhésion pour relance', '', 0),
(63, 'pmb', 'pret_adhesion_depassee', '1', 'Prêts si adhésion dépassée : 0 INTERDIT incontournable, 1 POSSIBLE', '', 0),
(64, 'pdflettreadhesion', 'fdp', 'Le responsable.', 'Formule de politesse en bas de page', '', 0),
(65, 'pdflettreadhesion', 'madame_monsieur', 'Madame, Monsieur,', 'Civilité du destinataire', '', 0),
(66, 'pdflettreadhesion', 'texte', 'Votre abonnement arrive à échéance le !!date_fin_adhesion!!. Nous vous remercions de penser à le renouveler lors de votre prochaine visite.\r\n\r\nNous vous prions de recevoir, Madame, Monsieur, l\'expression de nos meilleures salutations.\r\n\r\n\r\n', 'Phrase d\'introduction de l\'échéance de l\'abonnement', '', 0),
(67, 'pdflettreadhesion', 'marge_page_gauche', '10', 'Marge gauche de la page en mm', '', 0),
(68, 'pdflettreadhesion', 'marge_page_droite', '10', 'Marge droite de la page en mm', '', 0),
(69, 'pdflettreadhesion', 'largeur_page', '210', 'Largeur de la page en mm', '', 0),
(70, 'pdflettreadhesion', 'hauteur_page', '297', 'Hauteur de la page en mm', '', 0),
(71, 'pdflettreadhesion', 'format_page', 'P', 'P pour Portrait, L pour paysage (Landscape)', '', 0),
(72, 'mailrelanceadhesion', 'objet', '$biblio_name : votre abonnement', 'Objet du courrier de relance d\'adhésion. Utilisez biblio_name pour reprendre le nom précisé dans la localisation des exemplaires.', '', 0),
(73, 'mailrelanceadhesion', 'texte', 'Votre abonnement arrive à échéance le !!date_fin_adhesion!!. Nous vous remercions de penser à le renouveler lors de votre prochaine visite.\r\n\r\nCordialement,\r\n\r\n', 'Texte de la relance, !!date_fin_adhesion!! sera remplacé à l\'édition par la date de fin d\'adhésion du lecteur', '', 0),
(74, 'mailrelanceadhesion', 'madame_monsieur', 'Madame, Monsieur,', 'Entête du courrier de relance d\'adhésion', '', 0),
(75, 'mailrelanceadhesion', 'fdp', 'Le responsable.', 'Formule de politesse en bas de page', '', 0),
(76, 'opac', 'show_marguerite_browser', '0', '0 ou 1 : marguerite des catégories', 'f_modules', 0),
(77, 'opac', 'show_100cases_browser', '0', '0 ou 1 : affichage de 100 catégories', 'f_modules', 0),
(78, 'pmb', 'indexint_decimal', '1', '0 ou 1 : l\'indexation interne est-elle une cotation décimale type Dewey', '', 0),
(79, 'opac', 'modules_search_indexint', '1', 'Recherche simple dans les indexations décimales :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(80, 'empr', 'birthdate_optional', '1', 'Année de naissance facultative : \r\n 0 > non:elle est obligatoire \r\n 1 Oui', '', 0),
(81, 'thesaurus', 'categories_show_empty_categ', '1', 'Affichage des catégories ne contenant aucune notice :\r\n0=non, 1=oui', 'categories', 0),
(82, 'thesaurus', 'categories_term_search_n_per_page', '50', 'Nombre de termes affichés par page lors d\'une recherche par terme dans les catégories', 'categories', 0),
(83, 'opac', 'show_loginform', '1', 'Affichage du login lecteur dans l\'OPAC \r\n 0 > non\r\n 1 Oui', 'f_modules', 0),
(84, 'opac', 'default_style', 'genbib', 'Style graphique de l\'OPAC, 1 style par défaut, nomargin : sans affichage du bandeau de gauche', 'a_general', 0),
(85, 'opac', 'show_exemplaires', '1', 'Afficher les exemplaires dans l\'OPAC\n 1 Oui,\n 0 : Non', 'e_aff_notice', 0),
(86, 'pmb', 'import_modele', 'func_bdp.inc.php', 'Quel script de fonctions d\'import utiliser pour personnaliser l\'import ?', '', 0),
(87, 'pmb', 'quotas_avances', '0', 'Quotas de prêts avancés ? \r\n 0 : Non\r\n 1 : Oui', '', 0),
(88, 'opac', 'logo', 'logo_default.jpg', 'Nom du fichier de l\'image logo', 'z_unused', 0),
(89, 'opac', 'logosmall', 'images/site/livre.png', 'Nom du fichier de l\'image petit logo', 'b_aff_general', 0),
(90, 'opac', 'show_bandeaugauche', '1', 'Affichage du bandeau de gauche ? \n 0 : Non\n 1 : Oui', 'f_modules', 0),
(91, 'opac', 'show_liensbas', '1', 'Affichage des liens(pmb, google, bibli) en bas de page ? \n 0 : Non\n 1 : Oui', 'f_modules', 0),
(92, 'opac', 'show_homeontop', '0', 'Affichage du lien HOME (retour accueil) sous le nom de la bibliothèque ou du centre de ressources (nécessaire si masquage bandeau gauche) ? \r\n 0 : Non\r\n 1 : Oui', 'f_modules', 0),
(93, 'pmb', 'resa_quota_pret_depasse', '1', 'Réservation possible même si quota de prêt dépassé ? \n 0 : Non\n 1 : Oui', '', 0),
(94, 'pmb', 'import_limit_read_file', '100', 'Limite de taille de lecture du fichier en import, en général 100 ou 200 doit fonctionner, si problème de time out : fixer plus bas, 50 par exemple.', '', 0),
(95, 'pmb', 'import_limit_record_load', '100', 'Limite de taille de traitement de notices en import, en général 100 ou 200 doit fonctionner, si problème de time out : fixer plus bas, 50 par exemple.', '', 0),
(96, 'opac', 'biblio_preamble_p1', '<img src=\"./styles/genbib/images/image1.jpg\" />\r\n<img src=\"./styles/genbib/images/image2.jpg\" />\r\n<img src=\"./styles/genbib/images/image3.jpg\" />', 'Paragraphe 1 d\'informations (par exemple, description du fonds)', 'b_aff_general', 0),
(97, 'opac', 'biblio_preamble_p2', '<ul id=\"menuDeroulant\">\r\n\r\n<li><a href=\"./index.php\">Accueil</a></li>\r\n<li><a href=\"./index.php?lvl=infopages&amp;pagesid=4\">Calendrier</a></li>\r\n<li><a href=\"./index.php?lvl=infopages&amp;pagesid=1\">Actualités BnF</a>\r\n<ul class=\"sousMenu\">\r\n<li><a href=\"./index.php?lvl=infopages&amp;pagesid=3\">Aide en ligne</a></li>\r\n<li><a href=\"./index.php?lvl=infopages&amp;pagesid=2\">Version 4.0 !</a></li>\r\n</ul>\r\n</li>\r\n\r\n<li><a href=\"./index.php?lvl=infopages&amp;pagesid=6\">Nous trouver</a></li>\r\n\r\n</ul>', 'Paragraphe 2 d\'informations : accueil du public.', 'b_aff_general', 0),
(98, 'opac', 'biblio_quicksummary_p1', '', 'Paragraphe 1 de résumé, est masqué par défaut dans la feuille de style, voir id quickSummary.p1', 'z_unused', 0),
(99, 'opac', 'biblio_quicksummary_p2', '', 'Paragraphe 2 de résumé, est masqué par défaut dans la feuille de style, voir id quickSummary.p2', 'z_unused', 0),
(100, 'opac', 'show_dernieresnotices', '0', 'Affichage des dernières notices créées en bas de page ? \n 0 : Non\n 1 : Oui', 'f_modules', 0),
(101, 'opac', 'show_etageresaccueil', '1', 'Affichage des étagères dans la page d\'accueil en bas de page ? \n 0 : Non\n 1 : Oui', 'f_modules', 0),
(102, 'opac', 'biblio_important_p1', '<a href=\"index.php?lvl=index\"><img src=\"./images/bar_spacer.gif\" id=\"map_lien_retour\" alt=\"\" /></a> \r\n<a href=\"http://www.sigb.net\" id=\"puce\">Sigb.Net</a>\r\n<a href=\"http://www.sigb.net/wiki/\" id=\"puce\">WiKi PMB</a>\r\n<a href=\"http://fr.wikipedia.org\" id=\"puce\">WiKipedia</a>\r\n', 'Infos importantes 1, dans la feuille de style, voir id important.p1\r\n\r\n', 'b_aff_general', 0),
(103, 'opac', 'biblio_important_p2', '', 'Infos importantes, dans la feuille de style, voir id important.p2', 'b_aff_general', 0),
(104, 'opac', 'biblio_name', 'Bib\'Doc', 'Nom de la bibliothèque ou du centre de ressources dans l\'opac', 'b_aff_general', 0),
(105, 'opac', 'biblio_website', 'http://www.sigb.net', 'Site web de la bibliothèque ou du centre de ressources dans l\'opac', 'b_aff_general', 0),
(106, 'opac', 'biblio_adr1', 'ZI de Mont/Loir\r\nBP 10023', 'Adresse 1 de la bibliothèque ou du centre de ressources dans l\'opac', 'b_aff_general', 0),
(107, 'opac', 'biblio_town', 'CHATEAU DU LOIR', 'Ville dans l\'opac', 'b_aff_general', 0),
(108, 'opac', 'biblio_cp', '72500', 'Code postal dans l\'opac', 'b_aff_general', 0),
(109, 'opac', 'biblio_country', 'France', 'Pays dans l\'opac', 'b_aff_general', 0),
(110, 'opac', 'biblio_phone', '02 43 440 660', 'Téléphone dans l\'opac', 'b_aff_general', 0),
(111, 'opac', 'biblio_dep', '72', 'Département dans l\'opac pour la météo', 'b_aff_general', 0),
(112, 'opac', 'biblio_email', 'pmb@sigb.net', 'Email de contact dans l\'opac', 'b_aff_general', 0),
(113, 'opac', 'etagere_notices_order', 'index_serie, tnvol, index_sew', 'Ordre d\'affichage des notices dans les étagères dans l\'opac \n  index_serie, tit1 : tri par titre de série et titre \n rand()  : aléatoire', 'j_etagere', 0),
(114, 'opac', 'etagere_notices_format', '4', 'Format d\'affichage des notices dans les étagères de l\'écran d\'accueil \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)', 'j_etagere', 0),
(115, 'opac', 'etagere_notices_depliables', '1', 'Affichage dépliable des notices dans les étagères de l\'écran d\'accueil \r\n 0 : Non \r\n 1 : Oui', 'j_etagere', 0),
(116, 'opac', 'etagere_nbnotices_accueil', '5', 'Nombre de notices affichées dans les étagères de l\'écran d\'accueil \r\n 0 : Toutes \r\n -1 : Aucune \r\n x : x notices affichées au maximum', 'j_etagere', 0),
(117, 'opac', 'nb_aut_rec_per_page', '15', 'Nombre de notices affichées pour une autorité donnée', 'd_aff_recherche', 0),
(118, 'opac', 'notices_format', '4', 'Format d\'affichage des notices en résultat de recherche\n 0 : Utiliser le paramètre notices_format_onglets\n 1 : ISBD seul\n 2 : Public seul \n4 : ISBD et Public\n 5 : ISBD et Public avec ISBD en premier \n8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)', 'e_aff_notice', 0),
(119, 'opac', 'notices_depliable', '1', 'Affichage dépliable des notices en résultat de recherche:\n0: Non dépliable\n1: Dépliable en cliquant que sur l\'icone\n2: Déplibable en cliquant sur toute la ligne du titre', 'e_aff_notice', 0),
(120, 'opac', 'term_search_n_per_page', '50', 'Nombre de termes affichés par page en recherche par terme', 'c_recherche', 0),
(121, 'opac', 'show_empty_categ', '1', 'En recherche par terme, affichage des catégories ne contenant aucun ouvrage :\r\n 0 : Non \r\n 1 : Oui', 'i_categories', 0),
(122, 'opac', 'allow_extended_search', '1', 'Autorisation ou non de la recherche avancée dans l\'OPAC \n 0 : Non \n 1 : Oui', 'c_recherche', 0),
(123, 'opac', 'allow_term_search', '1', 'Autorisation ou non de la recherche par termes dans l\'OPAC \n 0 : Non \n 1 : Oui', 'c_recherche', 0),
(124, 'opac', 'term_search_height', '350', 'Hauteur en pixels de la frame de recherche par termes (si pas précisé ou zéro : par défaut 200 pixels)', 'c_recherche', 0),
(125, 'opac', 'categories_nb_col_subcat', '3', 'Nombre de colonnes de sous-catégories en navigation dans les catégories \n 3 par défaut', 'i_categories', 0),
(126, 'opac', 'max_resa', '5', 'Nombre maximum de réservation sur un document \r\n 5 par défaut \r\n 0 pour illimité', 'a_general', 0),
(127, 'pmb', 'show_help', '1', 'Affichage de l\'aide contextuelle dans PMB en partie gestion \r\n 1 Oui \r\n 0 Non', '', 0),
(128, 'opac', 'show_help', '1', 'Affichage de l\'aide en ligne dans l\'OPAC de PMB  \n 1 Oui \n 0 Non', 'f_modules', 0),
(129, 'opac', 'cart_allow', '1', 'Paniers possibles dans l\'OPAC de PMB  \n 1 Oui \n 0 Non', 'f_modules', 0),
(130, 'opac', 'max_cart_items', '200', 'Nombre maximum de notices dans un panier utilisateur.', 'h_cart', 0),
(131, 'opac', 'show_section_browser', '1', 'Afficher le butineur de localisation et de sections ?\n 0 : Non\n 1 : Oui', 'f_modules', 0),
(132, 'opac', 'nb_localisations_per_line', '6', 'Nombre de localisations affichées par ligne en page d\'accueil (si show_section_browser=1)', 'k_section', 0),
(133, 'opac', 'nb_sections_per_line', '6', 'Nombre de sections affichées par ligne en visualisation de localisation (si show_section_browser=1)', 'k_section', 0),
(134, 'opac', 'cart_only_for_subscriber', '1', 'Paniers de notices réservés aux adhérents de la bibliothèque ou du centre de ressources ?\r\n 1: Oui\r\n 0: Non, autorisé pour tout internaute', 'h_cart', 0),
(135, 'opac', 'notice_reduit_format', '0', 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice', 'e_aff_notice', 0),
(136, 'pdflettreresa', 'before_list', 'Suite à votre demande de réservation, nous vous informons que le ou les ouvrages ci-dessous sont à votre disposition à la bibliothèque.', 'Texte apparaissant avant la liste des ouvrages en résa dans le courrier de confirmation de résa', '', 0),
(137, 'pdflettreresa', 'after_list', 'Passé le délai de réservation, ces ouvrages seront remis en circulation, vous priant de les retirer dans les meilleurs délais.', 'Texte apparaissant après la liste des ouvrages', '', 0),
(138, 'pdflettreresa', 'fdp', 'Le responsable.', 'Signataire de la lettre, utiliser $biblio_name pour reprendre le paramètre \"biblio name\" ou bien mettre autre chose.', '', 0),
(139, 'pdflettreresa', 'madame_monsieur', 'Madame, Monsieur,', 'Entête de la lettre', '', 0),
(140, 'pdflettreresa', 'nb_par_page', '7', 'Nombre d\'ouvrages réservés imprimé sur les pages suivantes.', '', 0),
(141, 'pdflettreresa', 'nb_1ere_page', '4', 'Nombre d\'ouvrages réservés imprimé sur la première page', '', 0),
(142, 'pdflettreresa', 'taille_bloc_expl', '20', 'Taille d\'un bloc (2 lignes) d\'ouvrage en réservation. Le début de chaque ouvrage en résa sera espacé de cette valeur sur la page', '', 0),
(143, 'pdflettreresa', 'debut_expl_1er_page', '160', 'Début de la liste des ouvrages sur la première page, en mm depuis le bord supérieur de la page. Doit être règlé en fonction du texte qui précède la liste des ouvrages, lequel peut être plus ou moins long.', '', 0),
(144, 'pdflettreresa', 'debut_expl_page', '15', 'Début de la liste des ouvrages sur les pages suivantes, en mm depuis le bord supérieur de la page.', '', 0),
(145, 'pdflettreresa', 'limite_after_list', '270', 'Position limite en bas de page. Si un élément imprimé tente de dépasser cette limite, il sera imprimé sur la page suivante.', '', 0),
(146, 'pdflettreresa', 'marge_page_gauche', '10', 'Marge de gauche en mm', '', 0),
(147, 'pdflettreresa', 'marge_page_droite', '10', 'Marge de droite en mm', '', 0),
(148, 'pdflettreresa', 'largeur_page', '210', 'Largeur de la page en mm', '', 0),
(149, 'pdflettreresa', 'hauteur_page', '297', 'Hauteur de la page en mm', '', 0),
(150, 'pdflettreresa', 'format_page', 'P', 'Format de la page : \r\n P : Portrait\r\n L : Landscape = paysage', '', 0),
(151, 'opac', 'categories_max_display', '200', 'Pour la page d\'accueil, nombre maximum de catégories principales affichées', 'i_categories', 0),
(152, 'opac', 'search_other_function', '', 'Fonction complémentaire pour les recherches en page d\'accueil', 'c_recherche', 0),
(153, 'opac', 'lien_bas_supplementaire', '<a href=\"./index.php?lvl=infopages&amp;pagesid=7\">Mentions légales</a>', 'Lien supplémentaire en bas de page d\'accueil, à renseigner complètement : a href= lien /a', 'b_aff_general', 0),
(154, 'z3950', 'import_modele', 'func_other.inc.php', 'Quel script de fonctions d\'import utiliser pour personnaliser l\'import en intégration z3950 ?', '', 0),
(155, 'ldap', 'server', 'chinon', 'Serveur LDAP, IP ou host', '', 0),
(156, 'ldap', 'basedn', '', 'Racine du nom de domaine LDAP', '', 0),
(157, 'ldap', 'port', '389', 'Port du serveur LDAP', '', 0),
(158, 'ldap', 'filter', '(&(objectclass=person)(gidnumber=GID))', 'Serveur LDAP, IP ou host', '', 0),
(159, 'ldap', 'fields', 'uid,gecos,departmentnumber', 'Champs du serveur LDAP', '', 0),
(160, 'ldap', 'lang', 'fr_FR', 'Langue du serveur LDAP', '', 0),
(161, 'ldap', 'groups', '', 'Groupes du serveur LDAP', '', 0),
(162, 'ldap', 'accessible', '0', 'LDAP accessible ?', '', 0),
(163, 'opac', 'categories_show_only_last', '0', 'Dans la fiche d\'une notice : \n 0 tout afficher \n 1 : afficher uniquement la dernière feuille de l\'arbre de la catégorie', 'i_categories', 0),
(164, 'thesaurus', 'categories_show_only_last', '0', 'Dans la fiche d\'une notice : \n 0 tout afficher \n 1 : afficher uniquement la dernière feuille de l\'arbre de la catégorie', 'categories', 0),
(165, 'pmb', 'prefill_cote', 'custom_cote_02.inc.php', 'Script personnalisé de construction de la cote de l\'exemplaire', '', 0),
(166, 'ldap', 'proto', '3', 'Version du protocole LDAP : 3 ou 2', '', 0),
(167, 'ldap', 'binddn', 'uid=UID,ou=People', 'Description de la liaison : construction de la chaine binddn pour lier l\'authentification au serveur LDAP dans l\'OPAC', '', 0),
(168, 'empr', 'corresp_import', '', 'Table de correspondances colonnes/champs en import de lecteurs à partir d\'un fichier ASCII', '', 1),
(169, 'pmb', 'type_audit', '0', 'Gestion/affichage des dates de création/modification \n 0: Rien\n 1: Création et dernière modification\n 2: Création et toutes les dates de modification', '', 0),
(170, 'pmb', 'gestion_abonnement', '0', 'Utiliser la gestion des abonnements des lecteurs ? \n 0 : Non\n 1 : Oui, gestion simple, \n 2 : Oui, gestion avancée', '', 0),
(171, 'pmb', 'utiliser_calendrier', '0', 'Utiliser le calendrier des jours d\'ouverture ?\n 0 : non\n 1 : oui, pour le calcul des dates de retour et des retards\n 2 : oui, pour le calcul des dates de retour uniquement', '', 0),
(172, 'pmb', 'gestion_financiere', '0', 'Utiliser le module gestion financière ? \n 0 : Non\n 1 : Oui', '', 0),
(173, 'pmb', 'gestion_tarif_prets', '0', 'Utiliser la gestion des tarifs de prêts ? \n 0 : Non\n 1 : Oui, gestion simple, \n 2 : Oui, gestion avancée', '', 0),
(174, 'pmb', 'gestion_amende', '0', 'Utiliser la gestion des amendes:\n 0 = Non\n 1 = Gestion simple\n 2 = Gestion avancée', '', 0),
(175, 'finance', 'amende_jour', '0.15', 'Amende par jour de retard pour tout type de document. Attention, le séparateur décimal est le point, pas la virgule', '', 1),
(176, 'finance', 'delai_avant_amende', '15', 'Délai avant déclenchement de l\'amende, en jour', '', 1),
(177, 'finance', 'delai_recouvrement', '7', 'Délai entre 3eme relance et mise en recouvrement officiel de l\'amende, en jour', '', 1),
(178, 'finance', 'amende_maximum', '0', 'Amende maximum, quel que soit le retard l\'amende est plafonnée à ce montant. 0 pour désactiver ce plafonnement.', '', 1),
(179, 'pdflettreresa', 'priorite_email', '1', 'Priorité des lettres de confirmation de réservation par mail lors de la validation d\'une réservation:\n 0 : Lettre seule \n 1 : Mail, à défaut lettre\n 2 : Mail ET lettre\n 3 : Aucune alerte', '', 0),
(180, 'pdflettreresa', 'priorite_email_manuel', '1', 'Priorité des lettres de confirmation de réservation par mail lors de l\'impression à partir du bouton :\n 0 : Lettre seule \n 1 : Mail, à défaut lettre\n 2 : Mail ET lettre\n 3 : Aucune alerte', '', 0),
(181, 'finance', 'blocage_abt', '1', 'Blocage du prêt si le compte abonnement est débiteur\n 0 : pas de blocage \n 1 : blocage avec forçage possible  : blocage incontournable.', '', 1),
(182, 'finance', 'blocage_pret', '1', 'Blocage du prêt si le compte prêt est débiteur\n 0 : pas de blocage \n 1 : blocage avec forçage possible  : blocage incontournable.', '', 1),
(183, 'finance', 'blocage_amende', '1', 'Blocage du prêt si le compte amende est débiteur\n 0 : pas de blocage \n 1 : blocage avec forçage possible  : blocage incontournable.', '', 1),
(184, 'pmb', 'gestion_devise', '&euro;', 'Devise de la gestion financière, ce qui va être affiché en code HTML', '', 0),
(185, 'opac', 'book_pics_url', 'http://images-eu.amazon.com/images/P/!!isbn!!.08.MZZZZZZZ.jpg', 'URL des vignettes des notices, dans le chemin fourni, !!isbn!! sera remplacé par le code ISBN ou EAN de la notice purgé de tous les tirets ou points. \r\n exemple : http://www.monsite/opac/images/vignettes/!!isbn!!.jpg', 'e_aff_notice', 0),
(186, 'opac', 'lien_moteur_recherche', '<a href=http://www.google.fr target=_blank>Faire une recherche avec Google</a>', 'Lien supplémentaire en bas de page d\'accueil, à renseigner complètement : a href= lien /a', 'b_aff_general', 0),
(187, 'pmb', 'pret_express_statut', '2', 'Statut de notice à utiliser en création d\'exemplaires en prêts express', '', 0),
(188, 'opac', 'notice_affichage_class', '', 'Nom de la classe d\'affichage pour personnalisation de l\'affichage des notices', 'e_aff_notice', 0),
(189, 'pmb', 'confirm_retour', '0', 'En retour de documents, le retour doit-il être confirmé ? \n 0 : Non, on peut passer les codes-barres les uns après les autres \n 1 : Oui, il faut valider le retour après chaque code-barre', '', 0),
(190, 'opac', 'show_meteo_url', '<img src=\"http://perso0.free.fr/cgi-bin/meteo.pl?dep=72\" alt=\"\" border=\"0\" hspace=0>', 'URL de la météo affichée', 'f_modules', 0),
(191, 'pmb', 'limitation_dewey', '5', 'Nombre maximum de caractères dans la Dewey (676) en import : \r\n 0 aucune limitation \r\n 3 : limitation de 000 à 999 \r\n 5 (exemple) limitation 000.0 \r\n -1 : aucune importation', '', 0),
(192, 'finance', 'delai_1_2', '15', 'Délai entre 1ere et 2eme relance', '', 1),
(193, 'finance', 'delai_2_3', '15', 'Délai entre 2eme et 3eme relance', '', 1),
(194, 'pmb', 'lecteurs_localises', '0', 'Lecteurs localisés ? \n 0: Non \n 1: Oui', '', 0),
(195, 'dsi', 'active', '1', 'D.S.I activée ? \n 0: Non \n 1: Oui', '', 0),
(196, 'dsi', 'auto', '1', 'D.S.I automatique activée ? \r\n 0: Non \r\n 1: Oui', '', 0),
(197, 'dsi', 'insc_categ', '0', 'Inscription automatique dans les bannettes de la catégorie du lecteur en création ? \n 0: Non \n 1: Oui', '', 0),
(198, 'opac', 'allow_bannette_priv', '1', 'Possibilité pour les lecteurs de créer ou modifier leurs bannettes privées\r\n 0: Non\r\n 1: Oui\r\n 2: Oui et le bouton de création s\'affiche en permanence en recherche multicritères', 'l_dsi', 0),
(199, 'opac', 'allow_resiliation', '1', 'Possibilité pour les lecteurs de résilier leur abonnement aux bannettes pro \r\n 0: Non \r\n 1: Oui', 'l_dsi', 0),
(200, 'opac', 'show_categ_bannette', '1', 'Affichage des bannettes de la catégorie du lecteur et possibilité de s\'y abonner \r\n 0: Non \r\n 1: Oui', 'l_dsi', 0),
(201, 'opac', 'url_base', './', 'URL de base de l\'opac : typiquement mettre l\'url publique web http://monsite/opac/ ne pas oublier le / final', 'a_general', 0),
(202, 'finance', 'relance_1', '0.60', 'Frais de la première lettre de relance', '', 1),
(203, 'finance', 'relance_2', '0.60', 'Frais de la deuxième lettre de relance', '', 1),
(204, 'finance', 'relance_3', '3.38', 'Frais de la troisième lettre de relance', '', 1),
(205, 'finance', 'statut_perdu', '', 'Statut (d\'exemplaire) perdu pour des ouvrages non rendus', '', 1),
(206, 'pdflettreretard', '2after_list', 'Nous vous remercions de prendre rapidement contact par téléphone au $biblio_phone ou par mail à $biblio_email pour étudier la possibilité de prolonger ces prêts ou de rapporter les ouvrages concernés.', 'Texte apparaissant après la liste des ouvrages en retard dans le courrier', '', 0),
(207, 'pdflettreretard', '2before_list', 'Sauf erreur de notre part, vous avez toujours en votre possession le ou les ouvrage(s) suivant(s) dont la durée de prêt est aujourd\'hui dépassée :', 'Texte apparaissant avant la liste des ouvrages en retard dans le courrier de relance de retard', '', 0),
(208, 'pdflettreretard', '2debut_expl_1er_page', '160', 'Début de la liste des exemplaires sur la première page, en mm depuis le bord supérieur de la page. Doit être règlé en fonction du texte qui précède la liste des ouvrages, lequel peut être plus ou moins long.', '', 0),
(209, 'pdflettreretard', '2debut_expl_page', '15', 'Début de la liste des exemplaires sur les pages suivantes, en mm depuis le bord supérieur de la page.', '', 0),
(210, 'pdflettreretard', '2fdp', 'Le responsable.', 'Signataire de la lettre.', '', 0),
(211, 'pdflettreretard', '2format_page', 'P', 'Format de la page : \r\n P : Portrait\r\n L : Landscape = paysage', '', 0),
(212, 'pdflettreretard', '2hauteur_page', '297', 'Hauteur de la page en mm', '', 0),
(213, 'pdflettreretard', '2largeur_page', '210', 'Largeur de la page en mm', '', 0),
(214, 'pdflettreretard', '2limite_after_list', '270', 'Position limite en bas de page. Si un élément imprimé tente de dépasser cette limite, il sera imprimé sur la page suivante.', '', 0),
(215, 'pdflettreretard', '2madame_monsieur', 'Madame, Monsieur,', 'Entête de la lettre', '', 0),
(216, 'pdflettreretard', '2marge_page_droite', '10', 'Marge de droite en mm', '', 0),
(217, 'pdflettreretard', '2marge_page_gauche', '10', 'Marge de gauche en mm', '', 0),
(218, 'pdflettreretard', '2nb_1ere_page', '4', 'Nombre d\'ouvrages en retard imprimé sur la première page', '', 0),
(219, 'pdflettreretard', '2nb_par_page', '7', 'Nombre d\'ouvrages en retard imprimé sur les pages suivantes.', '', 0),
(220, 'pdflettreretard', '2taille_bloc_expl', '16', 'Taille d\'un bloc (2 lignes) d\'ouvrage en retard. Le début de chaque ouvrage en retard sera espacé de cette valeur sur la page', '', 0),
(221, 'pdflettreretard', '3after_list', 'Nous vous remercions de prendre rapidement contact par téléphone au $biblio_phone ou par mail à $biblio_email pour étudier la possibilité de prolonger ces prêts ou de rapporter les ouvrages concernés.', 'Texte apparaissant après la liste des ouvrages en retard dans le courrier', '', 0),
(222, 'pdflettreretard', '3before_list', 'Sauf erreur de notre part, vous avez toujours en votre possession le ou les ouvrage(s) suivant(s) dont la durée de prêt est aujourd\'hui dépassée :', 'Texte apparaissant avant la liste des ouvrages en retard dans le courrier de relance de retard', '', 0),
(223, 'pdflettreretard', '3debut_expl_1er_page', '160', 'Début de la liste des exemplaires sur la première page, en mm depuis le bord supérieur de la page. Doit être règlé en fonction du texte qui précède la liste des ouvrages, lequel peut être plus ou moins long.', '', 0),
(224, 'pdflettreretard', '3debut_expl_page', '15', 'Début de la liste des exemplaires sur les pages suivantes, en mm depuis le bord supérieur de la page.', '', 0),
(225, 'pdflettreretard', '3fdp', 'Le responsable.', 'Signataire de la lettre.', '', 0),
(226, 'pdflettreretard', '3format_page', 'P', 'Format de la page : \r\n P : Portrait\r\n L : Landscape = paysage', '', 0),
(227, 'pdflettreretard', '3hauteur_page', '297', 'Hauteur de la page en mm', '', 0),
(228, 'pdflettreretard', '3largeur_page', '210', 'Largeur de la page en mm', '', 0),
(229, 'pdflettreretard', '3limite_after_list', '270', 'Position limite en bas de page. Si un élément imprimé tente de dépasser cette limite, il sera imprimé sur la page suivante.', '', 0),
(230, 'pdflettreretard', '3madame_monsieur', 'Madame, Monsieur,', 'Entête de la lettre', '', 0),
(231, 'pdflettreretard', '3marge_page_droite', '10', 'Marge de droite en mm', '', 0),
(232, 'pdflettreretard', '3marge_page_gauche', '10', 'Marge de gauche en mm', '', 0),
(233, 'pdflettreretard', '3nb_1ere_page', '4', 'Nombre d\'ouvrages en retard imprimé sur la première page', '', 0),
(234, 'pdflettreretard', '3nb_par_page', '7', 'Nombre d\'ouvrages en retard imprimé sur les pages suivantes.', '', 0),
(235, 'pdflettreretard', '3taille_bloc_expl', '16', 'Taille d\'un bloc (2 lignes) d\'ouvrage en retard. Le début de chaque ouvrage en retard sera espacé de cette valeur sur la page', '', 0),
(236, 'pdflettreretard', '3before_recouvrement', 'Sans nouvelles de votre part dans les sept jours, nous nous verrons contraints de déléguer au trésor public le recouvrement des ouvrages suivants :', 'Texte avant la liste des ouvrages en recouvrement', '', 0),
(237, 'opac', 'bannette_notices_order', ' index_serie, tnvol, index_sew ', 'Ordre d\'affichage des notices dans les bannettes dans l\'opac \n  index_serie, tnvol, index_sew : tri par titre de série et titre \n rand()  : aléatoire', 'l_dsi', 0),
(238, 'opac', 'bannette_notices_format', '8', 'Format d\'affichage des notices dans les bannettes \n 1 : ISBD seul \n 2 : Public seul \n 4 : ISBD et Public \n 8 : Réduit (titre+auteurs) seul\n 9 : Templates django (Spécifier le nom du répertoire dans le paramètre notices_format_django_directory)', 'l_dsi', 0),
(239, 'opac', 'bannette_notices_depliables', '1', 'Affichage dépliable des notices dans les bannettes \n 0 : Non \n 1 : Oui', 'l_dsi', 0),
(240, 'opac', 'bannette_nb_liste', '7', 'Nbre de notices par bannettes en affichage de la liste des bannettes \r\n 0 Toutes \r\n N : maxi N\r\n -1 : aucune', 'l_dsi', 0),
(241, 'opac', 'dsi_active', '1', 'DSI, bannettes accessibles par l\'OPAC ? \r\n 0 : Non \r\n 1 : Oui', 'l_dsi', 0),
(242, 'mailretard', '2after_list', 'Nous vous remercions de prendre rapidement contact par téléphone au $biblio_phone ou par mail à $biblio_email pour étudier la possibilité de prolonger ces prêts ou de rapporter les ouvrages concernés.', 'Texte apparaissant après la liste des ouvrages en retard dans le mail', '', 0),
(243, 'mailretard', '2before_list', 'Sauf erreur de notre part, vous avez toujours en votre possession le ou les ouvrage(s) suivant(s) dont la durée de prêt est aujourd\'hui dépassée :', 'Texte apparaissant avant la liste des ouvrages en retard dans le mail de relance de retard', '', 0),
(244, 'mailretard', '2fdp', 'Le responsable.', 'Signataire du mail de relance de retard', '', 0),
(245, 'mailretard', '2madame_monsieur', 'Madame, Monsieur', 'Entête du mail', '', 0),
(246, 'mailretard', '2objet', '$biblio_name : documents en retard', 'Objet du mail de relance de retard', '', 0),
(247, 'mailretard', '3after_list', 'Nous vous remercions de prendre rapidement contact par téléphone au $biblio_phone ou par mail à $biblio_email pour étudier la possibilité de prolonger ces prêts ou de rapporter les ouvrages concernés.', 'Texte apparaissant après la liste des ouvrages en retard dans le mail', '', 0),
(248, 'mailretard', '3before_list', 'Sauf erreur de notre part, vous avez toujours en votre possession le ou les ouvrage(s) suivant(s) dont la durée de prêt est aujourd\'hui dépassée :', 'Texte apparaissant avant la liste des ouvrages en retard dans le mail de relance de retard', '', 0),
(249, 'mailretard', '3fdp', 'Le responsable.', 'Signataire du mail de relance de retard', '', 0),
(250, 'mailretard', '3madame_monsieur', 'Madame, Monsieur', 'Entête du mail', '', 0),
(251, 'mailretard', '3objet', '$biblio_name : documents en retard', 'Objet du mail de relance de retard', '', 0),
(252, 'mailretard', '3before_recouvrement', 'Sans nouvelles de votre part dans les sept jours, nous nous verrons contraints de déléguer au trésor public le recouvrement des ouvrages suivants :', 'Texte avant la liste des ouvrages en recouvrement', '', 0),
(253, 'mailretard', 'priorite_email', '1', 'Priorité des lettres de retard lors des relances :\n 0 : Lettre seule \n 1 : Mail, à défaut lettre\n 2 : Mail ET lettre', '', 0),
(254, 'pmb', 'import_modele_lecteur', '', 'Modèle d\'import des lecteurs', '', 0),
(255, 'pmb', 'blocage_retard', '0', 'Bloquer le prêt d\'une durée équivalente au retard ? 0=non, 1=oui', '', 0),
(256, 'pmb', 'blocage_delai', '7', 'Délai à partir duquel le retard est pris en compte pour le blocage\n 0 : dès qu\'un prêt est en retard\n N : au bout de N jours de retard', '', 0),
(257, 'pmb', 'blocage_max', '60', 'Nombre maximum de jours bloqués\n 0 : pas de limite\n N : maxi N\n -1 : blocage levé dès qu\'il n\'y a plus de retard', '', 0),
(258, 'pmb', 'blocage_coef', '1', 'Coefficient de proportionnalité des jours de retard pour le blocage', '', 0),
(259, 'pmb', 'blocage_retard_force', '1', '1 = Le prêt peut-être forcé lors d\'un blocage du compte, 2 = Pas de forçage possible', '', 0),
(260, 'opac', 'etagere_order', ' name ', 'Tri des étagères dans l\'écran d\'accueil, \n name = par nom\n name DESC = par nom décroissant', 'j_etagere', 0),
(261, 'pmb', 'book_pics_show', '1', 'Affichage des couvertures de livres en gestion\r\n 1: oui  \r\n 0: non', '', 0),
(262, 'pmb', 'book_pics_url', 'http://images-eu.amazon.com/images/P/!!isbn!!.08.MZZZZZZZ.jpg', 'URL des vignettes des notices, dans le chemin fourni, !!isbn!! sera remplacé par le code ISBN ou EAN de la notice purgé de tous les tirets ou points. \r\n exemple : http://www.monsite/opac/images/vignettes/!!isbn!!.jpg', '', 0),
(263, 'pmb', 'opac_url', './opac_css/', 'URL de l\'OPAC vu depuis la partie gestion, par défaut ./opac_css/', '', 0),
(264, 'opac', 'resa_popup', '1', 'Demande de connexion sous forme de popup ? :\n 0 : Non\n 1 : Oui', 'a_general', 0),
(265, 'pmb', 'vignette_x', '100', 'Largeur de la vignette créée pour un exemplaire numérique image', '', 0),
(266, 'pmb', 'vignette_y', '100', 'Hauteur de la vignette créée pour un exemplaire numérique image', '', 0),
(267, 'pmb', 'vignette_imagemagick', '', 'Chemin de l\'exécutable ImageMagick (/usr/bin/imagemagick par exemple)', '', 0),
(268, 'opac', 'show_rss_browser', '0', 'Affichage des flux RSS du catalogue en page d\'accueil OPAC 1: oui  ou 0: non', 'f_modules', 0),
(269, 'pmb', 'mail_methode', 'php', 'Méthode d\'envoi des mails : \n php : fonction mail() de php\n smtp,hote:port,auth,user,pass,(ssl|tls) : en smtp, mettre O ou 1 pour l\'authentification... ', '', 0),
(270, 'opac', 'mail_methode', 'php', 'Méthode d\'envoi des mails : \n php : fonction mail() de php\n smtp,hote:port,auth,user,pass,(ssl|tls) : en smtp, mettre O ou 1 pour l\'authentification... ', 'a_general', 0),
(271, 'opac', 'search_show_typdoc', '1', 'Affichage de la restriction par type de document pour les recherches en page d\'accueil', 'c_recherche', 0),
(272, 'pmb', 'verif_on_line', '0', 'Dans le menu Administration > Outils > Maj Base : vérification d\'une version plus récente de PMB en ligne ? \r\n0 : non : si vous n\'êtes pas connecté à internet \r\n 1 : Oui : si vous avez une connexion à internet', '', 0),
(273, 'opac', 'show_languages', '1 fr_FR,it_IT,es_ES,ca_ES,en_UK,nl_NL,oc_FR', 'Afficher la liste déroulante de sélection de la langue ? \r\n 0 : Non \r\n 1 : Oui \r\nFaire suivre d\'un espace et des codes des langues possibles séparées par des virgules : fr_FR,it_IT,en_UK,nl_NL,oc_FR', 'a_general', 0),
(274, 'pmb', 'pdf_font', 'Helvetica', 'Police de caractères à chasse variable pour les éditions en pdf - Police Arial', '', 0),
(275, 'pmb', 'pdf_fontfixed', 'Courier', 'Police de caractères à chasse fixe pour les éditions en pdf - Police Courier', '', 0),
(276, 'z3950', 'debug', '0', 'Debugage (export fichier) des notices lues en Z3950 \r\n 0: Non \r\n 1: 0ui', '', 0),
(277, 'pmb', 'nb_lastnotices', '10', 'Nombre de dernières notices affichées en Catalogue - Dernières notices', '', 0),
(278, 'opac', 'show_dernieresnotices_nb', '10', 'Nombre de dernières notices affichées en Catalogue - Dernières notices', 'f_modules', 0),
(279, 'pmb', 'recouvrement_auto', '0', 'Par défaut passage en recouvrement proposé en gestion des relances si niveau=3 et devrait être en 4: \r\n 1: Oui, recouvrement proposé par défaut \r\n 0: Ne rien faire par défaut', '', 0),
(280, 'pmb', 'keyword_sep', ';', 'Séparateur des mots clés dans la partie indexation libre, espace ou ; ou , ou ...', '', 0),
(281, 'thesaurus', 'mode_pmb', '0', 'Niveau d\'utilisation des thésaurus.\n 0 : Un seul thésaurus par défaut.\n 1 : Choix du thésaurus possible.', 'thesaurus', 0),
(282, 'thesaurus', 'defaut', '1', 'Identifiant du thésaurus par défaut.', 'thesaurus', 0),
(283, 'thesaurus', 'liste_trad', 'fr_FR', 'Liste des langues affichées dans les thésaurus.\n(ex : fr_FR,en_UK,...,ar)', 'thesaurus', 0),
(284, 'opac', 'thesaurus', '0', 'Niveau d\'utilisation des thésaurus.\n 0 : Un seul thésaurus par défaut.\n 1 : Choix du thésaurus possible.', 'a_general', 0),
(285, 'acquisition', 'active', '0', 'Module acquisitions activé.\n 0 : Non.\n 1 : Oui.', '', 0),
(286, 'acquisition', 'gestion_tva', '0', 'Gestion de la TVA.\n 0 : Non.\n 1 : Oui, avec saisie des prix HT.\n 2 : Oui, avec saisie des prix TTC.', '', 0),
(287, 'acquisition', 'poids_sugg', 'U=1.00,E=0.70,V=0.00', 'Pondération des suggestions par défaut en pourcentage.\n U=Utilisateurs, E=Emprunteurs, V=Visiteurs.\n ex : U=1.00,E=0.70,V=0.00 \n', '', 0),
(288, 'acquisition', 'format', '8,CA,DD,BL,FA', 'Taille du Numéro et Préfixes des actes d\'achats.\nex : 8,CA,DD,BL,FA \n8 = Préfixe + 8 Chiffres\nCA=Commande Achat, DD=Demande de Devis,BL=Bon de Livraison, FA=Facture Achat \n', '', 0),
(289, 'acquisition', 'budget', '0', 'Utilisation d\'un budget pour les commandes.\n 0:optionnel\n 1:obligatoire', '', 0),
(290, 'acquisition', 'pdfcde_format_page', '210x297', 'Largeur x Hauteur de la page en mm', 'pdfcde', 0),
(291, 'acquisition', 'pdfcde_orient_page', 'P', 'Orientation de la page: P=Portrait, L=Paysage', 'pdfcde', 0),
(292, 'acquisition', 'pdfcde_marges_page', '10,20,10,10', 'Marges de page en mm : Haut,Bas,Droite,Gauche', 'pdfcde', 0),
(293, 'acquisition', 'pdfcde_pos_logo', '10,10,20,20', 'Position du logo: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur', 'pdfcde', 0),
(294, 'acquisition', 'pdfcde_pos_raison', '35,10,100,10,16', 'Position Raison sociale: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfcde', 0),
(295, 'acquisition', 'pdfcde_pos_date', '150,10,0,6,8', 'Position Date: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfcde', 0),
(296, 'acquisition', 'pdfcde_pos_adr_fac', '10,35,60,5,10', 'Position Adresse de facturation: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfcde', 0),
(297, 'acquisition', 'pdfcde_pos_adr_liv', '10,75,60,5,10', 'Position Adresse de livraison: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfcde', 0),
(298, 'acquisition', 'pdfcde_pos_adr_fou', '100,55,100,6,14', 'Position Adresse fournisseur: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfcde', 0),
(299, 'acquisition', 'pdfcde_pos_num', '10,110,0,10,16', 'Position numéro de commande: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfcde', 0),
(300, 'acquisition', 'pdfcde_text_size', '10', 'Taille de la police texte', 'pdfcde', 0),
(301, 'acquisition', 'pdfcde_text_before', '', 'Texte avant le tableau de commande', 'pdfcde', 0),
(302, 'acquisition', 'pdfcde_text_after', '', 'Texte après le tableau de commande', 'pdfcde', 0),
(303, 'acquisition', 'pdfcde_tab_cde', '5,10', 'Table de commandes: Hauteur ligne,Taille police', 'pdfcde', 0),
(304, 'acquisition', 'pdfcde_pos_tot', '10,40,5,10', 'Position total de commande: Distance par rapport au bord gauche de la page, Largeur, Hauteur ligne,Taille police', 'pdfcde', 0),
(305, 'acquisition', 'pdfcde_pos_footer', '15,8', 'Position bas de page: Distance par rapport au bas de page, Taille police', 'pdfcde', 0),
(306, 'acquisition', 'pdfcde_pos_sign', '10,60,5,10', 'Position signature: Distance par rapport au bord gauche de la page, Largeur, Hauteur ligne,Taille police', 'pdfcde', 0),
(307, 'acquisition', 'pdfcde_text_sign', 'Le responsable de la bibliothèque.', 'Texte signature', 'pdfcde', 0),
(308, 'acquisition', 'pdfdev_format_page', '210x297', 'Largeur x Hauteur de la page en mm', 'pdfdev', 0),
(309, 'acquisition', 'pdfdev_orient_page', 'P', 'Orientation de la page: P=Portrait, L=Paysage', 'pdfdev', 0),
(310, 'acquisition', 'pdfdev_marges_page', '10,20,10,10', 'Marges de page en mm : Haut,Bas,Droite,Gauche', 'pdfdev', 0),
(311, 'acquisition', 'pdfdev_pos_logo', '10,10,20,20', 'Position du logo: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur', 'pdfdev', 0),
(312, 'acquisition', 'pdfdev_pos_raison', '35,10,100,10,16', 'Position Raison sociale: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfdev', 0),
(313, 'acquisition', 'pdfdev_pos_date', '150,10,0,6,8', 'Position Date: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfdev', 0),
(314, 'acquisition', 'pdfdev_pos_adr_fac', '10,35,60,5,10', 'Position Adresse de facturation: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfdev', 0),
(315, 'acquisition', 'pdfdev_pos_adr_liv', '10,75,60,5,10', 'Position Adresse de livraison: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfdev', 0);
INSERT INTO `parametres` VALUES
(316, 'acquisition', 'pdfdev_pos_adr_fou', '100,55,100,6,14', 'Position Adresse fournisseur: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfdev', 0),
(317, 'acquisition', 'pdfdev_pos_num', '10,110,0,10,16', 'Position numéro de commande: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfdev', 0),
(318, 'acquisition', 'pdfdev_text_size', '10', 'Taille de la police texte', 'pdfdev', 0),
(319, 'acquisition', 'pdfdev_text_before', '', 'Texte avant le tableau de commande', 'pdfdev', 0),
(569, 'pmb', 'latest_order', 'notice_id desc', 'Tri des dernières notices ? \n notice_id desc : par id de notice décroissant: idéal mais peut être problématique après une migration ou un import \n create_date desc: par la colonne date de création.', '', 0),
(321, 'acquisition', 'pdfdev_text_after', '', 'Texte après le tableau de commande', 'pdfdev', 0),
(322, 'acquisition', 'pdfdev_tab_dev', '5,10', 'Table de commandes: Hauteur ligne,Taille police', 'pdfdev', 0),
(323, 'acquisition', 'pdfdev_pos_footer', '15,8', 'Position bas de page: Distance par rapport au bas de page, Taille police', 'pdfdev', 0),
(324, 'acquisition', 'pdfdev_pos_sign', '10,60,5,10', 'Position signature: Distance par rapport au bord gauche de la page, Largeur, Hauteur ligne,Taille police', 'pdfdev', 0),
(325, 'acquisition', 'pdfdev_text_sign', 'Le responsable de la bibliothèque.', 'Texte signature', 'pdfdev', 0),
(326, 'opac', 'export_allow', '1', 'Export de notices à partir de l\'opac : \n 0 : interdit \n 1 : pour tous \n 2 : pour les abonnés uniquement', 'a_general', 0),
(327, 'opac', 'resa_planning', '0', 'Utiliser un planning de réservation ? \n 0: Non \n 1: Oui', 'a_general', 0),
(328, 'opac', 'resa_contact', '<a href=\'mailto:pmb@sigb.net\'>pmb@sigb.net</a>', 'Code HTML d\'information sur la personne à contacter par exemple en cas de problème de réservation.', 'a_general', 0),
(329, 'opac', 'default_operator', '0', 'Opérateur par défaut. 0 : OR, 1 : AND.', 'c_recherche', 0),
(330, 'opac', 'modules_search_all', '2', 'Recherche simple dans l\'ensemble des champs :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(331, 'acquisition', 'pdfliv_format_page', '210x297', 'Largeur x Hauteur de la page en mm', 'pdfliv', 0),
(332, 'acquisition', 'pdfliv_orient_page', 'P', 'Orientation de la page: P=Portrait, L=Paysage', 'pdfliv', 0),
(333, 'acquisition', 'pdfliv_marges_page', '10,20,10,10', 'Marges de page en mm : Haut,Bas,Droite,Gauche', 'pdfliv', 0),
(334, 'acquisition', 'pdfliv_pos_raison', '10,10,100,10,16', 'Position Raison sociale: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfliv', 0),
(335, 'acquisition', 'pdfliv_pos_adr_liv', '10,20,60,5,10', 'Position Adresse de livraison: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfliv', 0),
(336, 'acquisition', 'pdfliv_pos_adr_fou', '110,20,100,5,10', 'Position éléments fournisseur: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfliv', 0),
(337, 'acquisition', 'pdfliv_pos_num', '10,60,0,6,14', 'Position numéro Commande/Livraison: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfliv', 0),
(338, 'acquisition', 'pdfliv_tab_liv', '5,10', 'Table de livraisons: Hauteur ligne,Taille police', 'pdfliv', 0),
(339, 'acquisition', 'pdfliv_pos_footer', '15,8', 'Position bas de page: Distance par rapport au bas de page, Taille police', 'pdfliv', 0),
(340, 'pmb', 'default_operator', '0', 'Opérateur par défaut. \n 0 : OR, \n 1 : AND.', '', 0),
(341, 'mailretard', 'priorite_email_3', '0', 'Faire le troisième niveau de relance par mail :\n 0 : Non, lettre \n 1 : Oui, par mail', '', 0),
(342, 'opac', 'show_suggest', '0', 'Proposer de faire des suggestions dans l\'OPAC.\n 0 : Non.\n 1 : Oui, avec authentification.\n 2 : Oui, sans authentification.', 'f_modules', 0),
(343, 'acquisition', 'email_sugg', '0', 'Information par email de l\'évolution des suggestions.\n 0 : Non\n 1 : Oui', '', 0),
(344, 'acquisition', 'pdfliv_text_size', '10', 'Taille de la police texte', 'pdfliv', 0),
(345, 'acquisition', 'pdfliv_pos_date', '170,10,0,6,8', 'Position Date: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfliv', 0),
(346, 'acquisition', 'pdffac_text_size', '10', 'Taille de la police texte', 'pdffac', 0),
(347, 'acquisition', 'pdffac_format_page', '210x297', 'Largeur x Hauteur de la page en mm', 'pdffac', 0),
(348, 'acquisition', 'pdffac_orient_page', 'P', 'Orientation de la page: P=Portrait, L=Paysage', 'pdffac', 0),
(349, 'acquisition', 'pdffac_marges_page', '10,20,10,10', 'Marges de page en mm : Haut,Bas,Droite,Gauche', 'pdffac', 0),
(350, 'acquisition', 'pdffac_pos_raison', '10,10,100,10,16', 'Position Raison sociale: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdffac', 0),
(351, 'acquisition', 'pdffac_pos_date', '170,10,0,6,8', 'Position Date: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdffac', 0),
(352, 'acquisition', 'pdffac_pos_adr_fac', '10,20,60,5,10', 'Position Adresse de facturation: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdffac', 0),
(353, 'acquisition', 'pdffac_pos_adr_fou', '110,20,100,5,10', 'Position éléments fournisseur: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdffac', 0),
(354, 'acquisition', 'pdffac_pos_num', '10,60,0,6,14', 'Position numéro Commande/Facture: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdffac', 0),
(355, 'acquisition', 'pdffac_tab_fac', '5,10', 'Table de facturation: Hauteur ligne,Taille police', 'pdffac', 0),
(356, 'acquisition', 'pdffac_pos_tot', '10,40,5,10', 'Position total de commande: Distance par rapport au bord gauche de la page, Largeur, Hauteur ligne,Taille police', 'pdffac', 0),
(357, 'acquisition', 'pdffac_pos_footer', '15,8', 'Position bas de page: Distance par rapport au bas de page, Taille police', 'pdffac', 0),
(358, 'acquisition', 'pdfsug_text_size', '8', 'Taille de la police texte', 'pdfsug', 0),
(359, 'acquisition', 'pdfsug_format_page', '210x297', 'Largeur x Hauteur de la page en mm', 'pdfsug', 0),
(360, 'acquisition', 'pdfsug_orient_page', 'P', 'Orientation de la page: P=Portrait, L=Paysage', 'pdfsug', 0),
(361, 'acquisition', 'pdfsug_marges_page', '10,20,10,10', 'Marges de page en mm : Haut,Bas,Droite,Gauche', 'pdfsug', 0),
(362, 'acquisition', 'pdfsug_pos_titre', '10,10,100,10,16', 'Position titre: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfsug', 0),
(363, 'acquisition', 'pdfsug_pos_date', '170,10,0,6,8', 'Position Date: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfsug', 0),
(364, 'acquisition', 'pdfsug_tab_sug', '5,10', 'Table de suggestions: Hauteur ligne,Taille police', 'pdfsug', 0),
(365, 'acquisition', 'pdfsug_pos_footer', '15,8', 'Position bas de page: Distance par rapport au bas de page, Taille police', 'pdfsug', 0),
(366, 'acquisition', 'mel_rej_obj', 'Rejet suggestion', 'Objet du mail de rejet de suggestion', 'mel', 0),
(367, 'acquisition', 'mel_rej_cor', 'Votre suggestion du !!date!! est rejetée.\n\n', 'Corps du mail de rejet de suggestion', 'mel', 0),
(368, 'acquisition', 'mel_con_obj', 'Confirmation suggestion', 'Objet du mail de confirmation de suggestion', 'mel', 0),
(369, 'acquisition', 'mel_con_cor', 'Votre suggestion du !!date!! est retenue pour un prochain achat.\n\n', 'Corps du mail de confirmation de suggestion', 'mel', 0),
(370, 'acquisition', 'mel_aba_obj', 'Abandon suggestion', 'Objet du mail d\'abandon de suggestion', 'mel', 0),
(371, 'acquisition', 'mel_aba_cor', 'Votre suggestion du !!date!! n\'est pas retenue ou n\'est pas disponible à la vente.\n\n', 'Corps du mail d\'abandon de suggestion', 'mel', 0),
(372, 'acquisition', 'mel_cde_obj', 'Commande suggestion', 'Objet du mail de commande de suggestion', 'mel', 0),
(373, 'acquisition', 'mel_cde_cor', 'Votre suggestion du !!date!! est en commande.\n\n', 'Corps du mail de commande de suggestion', 'mel', 0),
(374, 'acquisition', 'mel_rec_obj', 'Réception suggestion', 'Objet du mail de réception de suggestion', 'mel', 0),
(375, 'acquisition', 'mel_rec_cor', 'Votre suggestion du !!date!! a été reçue et sera bientôt disponible en réservation.\n\n', 'Corps du mail de réception de suggestion', 'mel', 0),
(376, 'opac', 'allow_tags_search', '0', 'Recherche par tag (mots clés utilisateurs) \n 1 = oui \n 0 = non', 'c_recherche', 0),
(377, 'opac', 'allow_add_tag', '0', 'Permettre aux utilisateurs d\'ajouter un tag à une notice.\n 0 : non\n 1 : oui\n 2 : identification obligatoire pour ajouter', 'a_general', 0),
(378, 'opac', 'avis_allow', '0', 'Permet de consulter/ajouter un avis pour les notices \n 0 : non \n 1 : sans être identifié : consultation possible, ajout impossible \n 2 : identification obligatoire pour consulter et ajouter \n 3 : consultation et ajout anonymes possibles', 'a_general', 0),
(1107, 'thesaurus', 'classement_location', '0', 'Utiliser la gestion des plans de classement localisés?\n 0: Non\n 1: Oui', 'classement', 0),
(379, 'opac', 'avis_nb_max', '30', 'Nombre maximal de commentaires conservés par notice. Les plus vieux sont effacés au profit des plus récents quand ce nombre est atteint.', 'a_general', 0),
(380, 'pmb', 'show_rtl', '0', 'Affichage possible de droite a gauche \r\n 0 non \r\n 1 oui', '', 0),
(381, 'opac', 'avis_show_writer', '0', 'Afficher le rédacteur de l\'avis \r\n 0 : non \r\n 1 : Prénom NOM \r\n 2 : login OPAC uniquement\r\n 3 : Prénom uniquement', 'a_general', 0),
(382, 'pmb', 'form_editables', '1', 'Grilles de notices éditables \r\n 0 non \r\n 1 oui', '', 0),
(383, 'acquisition', 'sugg_to_cde', '0', 'Transfert des suggestions en commande.\n 0 : Non.\n 1 : Oui.', '', 0),
(384, 'thesaurus', 'categories_categ_in_line', '0', 'Affichage des catégories en ligne.\n 0 : Non.\n 1 : Oui.', 'categories', 0),
(385, 'opac', 'categories_categ_in_line', '0', 'Affichage des catégories en ligne.\r\n 0 : Non.\r\n 1 : Oui.', 'i_categories', 0),
(386, 'pmb', 'label_construct_script', '', 'Script de construction d\'étiquette de cote', '', 0),
(387, 'dsi', 'func_after_diff', '', 'Script à exécuter après diffusion d\'une bannette', '', 0),
(388, 'opac', 'notice_groupe_fonction', '', 'Quel fichier/fonction inclure pour la présentation des resultats si toutes les notices d\'une recherche sont parmi les types mentionnés \n exemple : a,b text;c,d music;k photo fera include(text.inc.php) et appel à la fonction text()', 'd_aff_recherche', 0),
(389, 'opac', 'photo_mean_size_x', '', 'Taille X de la photo format \'moyen\', si vide, pas de redimensionnement', 'm_photo', 0),
(390, 'opac', 'photo_mean_size_y', '', 'Taille Y de la photo format \'moyen\', si vide, pas de redimensionnement', 'm_photo', 0),
(391, 'opac', 'photo_watermark', '', 'Watermark à ajouter sur les photos, si vide, pas de watermark', 'm_photo', 0),
(392, 'opac', 'photo_show_form', '', 'Afficher le formulaire de commande de photo ? \n 0: Non \n 1:Oui', 'm_photo', 0),
(393, 'opac', 'photo_email_form', '', 'Emails des destinataires des commandes de photo à séparer par des espaces si multiples.', 'm_photo', 0),
(394, 'opac', 'photo_watermark_transparency', '50', 'Transparence du watermark de 0 à 100 en %', 'm_photo', 0),
(395, 'opac', 'show_onglet_empr', '0', 'Afficher l\'onglet de compte emprunteur avec les onglets de recherche ? \n 0: Non \n 1: Oui \n 2: n\'afficher l\'onglet empr que lorsque l\'utilisateur est authentifié (et dans ce cas le clic sur l\'onglet mène vers empr.php)', 'f_modules', 0),
(396, 'pmb', 'url_base', 'http://SERVER/DIRECTORY/', 'URL de base de la gestion : typiquement mettre l\'url http://monserveur/pmb/ ne pas oublier le / final', '', 0),
(397, 'opac', 'show_empr', '0', 'Afficher l\'emprunteur actuel dans la liste des exemplaires ?\n 0 : non\n 1 : pour les abonnés\n 2 : pour tout le monde', 'a_general', 0),
(398, 'opac', 'show_login_form_next', '', 'Après connexion de l\'emprunteur se diriger vers quel module ? \n Vide = Compte emprunteur \n index.php = Retour en accueil', 'f_modules', 0),
(399, 'opac', 'allow_term_troncat_search', '0', 'Troncature automatique à droite \n 1 = oui \n 0 = non', 'c_recherche', 0),
(400, 'opac', 'show_results_first_page', '1', 'Affichage de résultats sur la première page lors d\'une recherche pour tous les champs \r\n 0=non \r\n 1=oui.', 'd_aff_recherche', 0),
(401, 'opac', 'nb_results_first_page', '10', 'Nombres de notices à afficher lors d\'une recherche pour le critère Tous les champs.', 'd_aff_recherche', 0),
(402, 'opac', 'show_infobulles_categ', '0', 'Affichage des infobulles sur les libellés des catégories. \n 0=non \n 1=oui', 'i_categories', 0),
(403, 'acquisition', 'sugg_display', '', 'Nom de la fonction personnalisée d\'affichage des suggestions', '', 0),
(404, 'acquisition', 'sugg_categ', '0', 'Affectation des suggestions à une catégorie de suggestions.\n 0 : Non\n 1 : Oui', '', 0),
(405, 'acquisition', 'sugg_categ_default', '1', 'Identifiant de la catégorie de suggestions par défaut.', '', 0),
(406, 'opac', 'sugg_categ', '0', 'Affectation des suggestions à une catégorie de suggestions.\n 0 : Non.\n 1 : Oui.', 'a_general', 0),
(407, 'opac', 'sugg_categ_default', '1', 'Identifiant de la catégorie de suggestions par défaut.', 'a_general', 0),
(408, 'acquisition', 'pdfsug_print', '', 'Quel script utiliser pour personnaliser l\'impression des listes de suggestions ?', 'pdfsug', 0),
(409, 'acquisition', 'pdfdev_print', '', 'Quel script utiliser pour personnaliser l\'impression des devis ?', 'pdfdev', 0),
(410, 'acquisition', 'pdfcde_print', '', 'Quel script utiliser pour personnaliser l\'impression des commandes ?', 'pdfcde', 0),
(411, 'acquisition', 'pdfliv_print', '', 'Quel script utiliser pour personnaliser l\'impression des bons de livraison ?', 'pdfliv', 0),
(412, 'acquisition', 'pdffac_print', '', 'Quel script utiliser pour personnaliser l\'impression des factures ?', 'pdffac', 0),
(413, 'pmb', 'notice_reduit_format', '0', 'Format d\'affichage des réduits de notices :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice', '', 0),
(414, 'pmb', 'resa_planning', '0', 'Utiliser un planning de réservation ? \n 0: Non \n 1: Oui', '', 0),
(415, 'pmb', 'antivol', '0', 'Système magnétique antivol à télécommander ? \n 1 Oui \n 0 Non', '', 0),
(416, 'acquisition', 'custom_calc_numero', '', 'Fonction personnalisée de numérotation des actes d\'achats.', '', 0),
(417, 'pmb', 'numero_exemplaire_auto', '0', 'Autorise la numérotation automatique d\'exemplaire ? \n 0 : non\n 1 : Oui, pour monographies et bulletins\n 2 : Oui, pour monographies seules\n 3 : Oui, pour bulletins seuls', '', 0),
(418, 'pmb', 'numero_exemplaire_auto_script', 'gen_code/gen_code_exemplaire.php', 'Nom du fichier de Script php pour la génération des codes d\'exemplaires en automatique', '', 0),
(419, 'empr', 'lecteur_controle_doublons', '0', 'Contrôle sur les doublons de lecteurs:\r\n0 : pas de controle sur les doublons, en saisie de fiche de lecteur. \r\n1,empr_nom,empr_prenom,... : recherche doublons sur les champs \'empr\', \r\n2,empr_nom,empr_prenom,... : recherche doublons sur les champs \'empr\', et champ personnalisables.\r\n3,empr_nom, empr_prenom ,... : idem, en rajoutant le test sur le groupe.', '', 0),
(420, 'pmb', 'pret_nombre_prolongation', '3', 'Nombre de prolongations autorisées', '', 0),
(421, 'pmb', 'pret_restriction_prolongation', '0', '0 : pas de restriction\r\n1 : prolongation limitée au paramètre pret_nombre_prolongation \r\n2 : prolongation gérée par les quotas ', '', 0),
(422, 'opac', 'pret_prolongation', '0', '0 : pas de prolongation\r\n1 : prolongation autorisée', 'a_general', 0),
(423, 'mailretard', '1after_list_group', 'Nous vous remercions de prendre rapidement contact par téléphone au 02 43 440 660 ou par mail à pmb@sigb.net pour étudier la possibilité de prolonger les emprunts de votre groupe ou de rapporter les ouvrages concernés.', 'Texte apparaissant après la liste des ouvrages en retard dans le mail', '', 0),
(424, 'mailretard', '1before_list_group', 'Sauf erreur de notre part, les emprunteurs de votre groupe ont toujours en leur possession le ou les ouvrage(s) suivant(s) dont la durée de prêt est aujourd\'hui dépassée :', 'Texte apparaissant avant la liste des ouvrages en retard dans le mail de relance de retard', '', 0),
(425, 'mailretard', '1fdp_group', 'Le responsable.', 'Signataire du mail de relance de retard', '', 0),
(426, 'mailretard', '1madame_monsieur_group', 'Madame, Monsieur', 'Entête du mail', '', 0),
(427, 'mailretard', '1objet_group', 'Bibliothèque test de PMB : documents en retard', 'Objet du mail de relance de retard', '', 0),
(428, 'pmb', 'first_week_day_format', '0', 'Format de la semaine: \n 0, la semaine commence le lundi \n 1 la semaine commence le dimanche', '', 0),
(429, 'opac', 'export_allow_expl', '0', 'Exporter les exemplaires et les documents numériques avec les notices :\n 0 : Aucun\n 1 : Uniquement les exemplaires\n 2 : Uniquement les documents numériques\n 3 : Les exemplaires et les documents numériques', 'a_general', 0),
(430, 'opac', 'nb_max_tri', '50', 'Nombre maximum de notices pour lesquelles le tri est autorisé.', 'c_recherche', 0),
(431, 'pmb', 'nb_max_tri', '50', 'Nombre maximum de notices pour lesquelles le tri est autorisé.', '', 0),
(432, 'opac', 'pret_duree_prolongation', '15', 'Nombre de jours de prolongation autorisé', 'a_general', 0),
(433, 'opac', 'surlignage', '2', 'Surligner les mots recherchés :\r\n0 : pas de surlignage\r\n1 : surlignage obligatoire\r\n2 : surlignage activable\r\n3 : surlignage désactivable', 'd_aff_recherche', 0),
(434, 'opac', 'nb_max_criteres_tri', '3', 'Nombre maximum de critères de tri à afficher.', 'c_recherche', 0),
(435, 'empr', 'show_caddie', '1', 'Afficher le module de paniers de lecteurs: \r\n 0: Non \r\n 1: Oui', '', 0),
(436, 'empr', 'pics_url', '', 'URL des photos des emprunteurs, dans le chemin fourni, !!num_carte!! sera remplacé par le numéro de carte du lecteur. \n exemple : http://www.monsite/photos/lecteurs/!!num_carte!!.jpg', '', 0),
(437, 'empr', 'pics_max_size', '100', 'Taille maximale des photos des emprunteurs, en largeur ou en hauteur', '', 0),
(438, 'thesaurus', 'classement_mode_pmb', '0', 'Niveau d\'utilisation des plans de classement des indexations. \n 0 : Un seul plan de classement. \n 1 : Choix du plan de classement possible.', 'classement', 0),
(439, 'thesaurus', 'classement_defaut', '1', 'Identifiant du plan de classement par défaut.', 'classement', 0),
(440, 'empr', 'electronic_loan_ticket', '0', 'Envoyer un ticket de prêt électronique ? \n 0: Non, \n 1: Oui', '', 0),
(441, 'empr', 'electronic_loan_ticket_obj', '!!biblio_name!! : emprunt(s) du !!date!!', 'Objet du mail de ticket électronique de prêt', '', 0),
(442, 'empr', 'electronic_loan_ticket_msg', 'Bonjour, <br />Voici la liste de vos emprunts et/ou réservations en date du !!date!! :<br /><br />!!all_loans!! !!all_reservations!!<br />Retrouvez toutes ces informations sur votre compte à l\'adresse <a href=!!biblio_website!!>!!biblio_website!!</a>.', 'Corps du mail de ticket électronique de prêt', '', 0),
(443, 'pmb', 'droits_explr_localises', '0', 'Les droits de gestion des exemplaires sont-ils localisés ? \n 0: Non \n 1: Oui', '', 0),
(444, 'empr', 'fiche_depliee', '1', 'La fiche emprunteur sera automatiquement : \n 0 : pliée \n 1 : dépliée', '', 0),
(445, 'empr', 'statut_adhes_depassee', '2', 'id du statut pour lequel les emprunteurs dont la date d\'adhesion est dépassée n\'apparaissent pas en zone d\'alerte', '', 0),
(446, 'opac', 'authorized_styles', 'pmb34,bueil,genbib', 'Styles de l\'OPAC autorisés, séparés par une virgule', 'a_general', 0),
(447, 'empr', 'relance_adhesion', '0', 'Les relances d\'adhésion sont envoyées : \n 0 : exclusivement par lettre \n 1 : mail, à défaut par lettre', '', 0),
(448, 'empr', 'show_rows', 'b,n,a,v,y,s,1', 'Colonnes affichées en liste de lecteurs, saisir les colonnes séparées par des virgules. Les colonnes disponibles pour l\'affichage de la liste des emprunteurs sont : \n n: nom+prénom \n a: adresse \n b: code-barre \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n 1: icône panier', '', 0),
(449, 'empr', 'sort_rows', 'n,c,l,s', 'Colonnes qui seront disponibles pour le tri des emprunteurs. Les colonnes possibles sont : \n n: nom+prénom \n b: code-barres \n c: catégories \n g: groupes \n l: localisation \n s: statut \n cp: code postal \n v: ville \n y: année de naissance \n ab: type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n #p[n] : [n] = id des champs personnalisés prêts', '', 0),
(450, 'empr', 'filter_rows', 'cp,v,y,s', 'Colonnes disponibles pour filtrer la liste des emprunteurs : \n v: ville\n l: localisation\n c: catégorie\n s: statut\n g: groupe\n y: année de naissance\n cp: code postal\n cs : code statistique\n ab : type d\'abonnement \n #e[n] : [n] = id des champs personnalisés lecteurs \n #p[n] : [n] = id des champs personnalisés prêts', '', 0),
(451, 'empr', 'header_format', '', 'Champs qui seront affichés dans l\'entête de la fiche emprunteur. Séparer les valeurs par des virgules. \nPour les champs personnalisés, saisir les identifiants. Les autres valeurs possibles sont les propriétés de la classe PHP \"pmb/opac_css/classes/emprunteur.class.php\".', '', 0),
(452, 'empr', 'archivage_prets', '0', 'Archiver les prêts des emprunteurs ? \n 0: Non \n 1: Oui\nATTENTION pour la France: nous attirons votre attention sur l\'obligation de déclarer votre traitement à la CNIL (www.cnil.fr) si vous activez cette fonctionnalité.', '', 0),
(453, 'empr', 'archivage_prets_purge', '0', 'Nombre de jours maximum où doivent être conservées les archives nominatives de prêts : \n0: illimité \nN: N jours', '', 0),
(454, 'opac', 'autres_lectures', '0', 'Afficher les emprunts des autres lecteurs du document courant ? \n 0: Non \n 1: Oui', 'f_modules', 0),
(455, 'opac', 'autres_lectures_tri', 'rand()', 'Tri des autres lectures proposées : \n rand(): aléatoire \n tit: par Titre', 'f_modules', 0),
(456, 'opac', 'autres_lectures_nb_mini_emprunts', '100', 'Nombre minimum d\'emprunts pour être comptabilisés \n 1: un seul emprunt suffit pour proposer la notice comme lecture associée \n N: N emprunts minimum nécessaires ', 'f_modules', 0),
(457, 'opac', 'autres_lectures_nb_maxi', '0', 'Nombre maximum de lectures associées proposées', 'f_modules', 0),
(458, 'opac', 'autres_lectures_nb_jours_maxi', '1', 'Délai en jours au delà duquel les emprunts ne sont pas comptabilisés, 0 pour illimité', 'f_modules', 0),
(459, 'opac', 'empr_hist_nb_max', '0', 'Nombre maximum de prêts précédents à afficher, 0 pour illimité', 'a_general', 0),
(460, 'opac', 'empr_hist_nb_jour_max', '1', 'Délai en jours au delà duquel les prêts précédents ne sont pas affichés, 0 pour illimité', 'a_general', 0),
(461, 'opac', 'allow_tags_search_min_occ', '1', 'Nombre mini d\'occurence d\'un tag pour être affiché, 1 pour tous', 'c_recherche', 0),
(462, 'pmb', 'etat_collections_localise', '0', 'L\'état des collections est-il localisé ? \n 0 : non \n 1 : oui', '', 0),
(463, 'pmb', 'clean_nb_elements', '100', 'Nombre d\'éléments traités par passe en nettoyage de base', '', 0),
(464, 'pmb', 'rfid_activate', '0', 'Enregistrements des prêts par platine RFID ? \n 0: Non \n 1: Oui', '', 0),
(465, 'opac', 'bull_results_per_page', '12', 'Nombre de bulletins affichés par page dans l\'affichage d\'un périodique', 'e_aff_notice', 0),
(466, 'pmb', 'rfid_serveur_url', '', 'URL du serveur de webservices RFID', '', 0),
(467, 'opac', 'authorized_information_pages', '1', 'Pages \"includable\" dans la page de l\'opac ./index.php?lvl=information&askedpage= : \n Mettre les noms des fichiers séparés par une virgule', 'a_general', 0),
(468, 'pmb', 'notice_controle_doublons', '0', 'Contrôle sur les doublons en saisie de la notice \n 0: Pas de contrôle sur les doublons, \n 1,tit1,tit2, ... : Recherche par méthode _exacte_ de doublons sur des champs, défini dans le fichier notice.xml  \n 2,tit1,tit2, ... : Recherche par _similitude_ ', '', 0),
(469, 'opac', 'title_ponderation', '0.5', 'Majoration de la pondération des mots du titre \n   mettre 0 (zero) pour interdire la majoration \n ATTENTION utiliser le point décimal ', 'c_recherche', 0),
(470, 'pmb', 'title_ponderation', '0.5', 'Majoration de la pondération des mots du titre \n   mettre 0 (zero) pour interdire la majoration \n ATTENTION utiliser le point décimal ', '', 0),
(471, 'pmb', 'param_etiq_codes_barres', '', 'Paramètres de sauvegarde des paramètres d\'édition d\'étiquettes codes-barres', '', 1),
(472, 'pmb', 'javascript_office_editor', '', 'Code HTML à insérer pour remplacer les textarea par un éditeur Office javascript', '', 0),
(473, 'opac', 'biblio_post_adress', '', 'Bloc d\'information après le bloc adresse, dans la feuille de style, voir id post_adress', 'b_aff_general', 0),
(474, 'opac', 'allow_external_search', '1', 'Autorisation ou non de la recherche par connecteurs externes dans l\'OPAC \r\n 0 : Non \r\n 1 : Oui', 'c_recherche', 0),
(475, 'pmb', 'nb_noti_calc_empty_words', '50', 'Un mot sera considéré comme vide s\'il apparaît dans un nombre de notices minimum. Saisir le pourcentage par rapport au nombre de notices total.', '', 1),
(476, 'opac', 'fonction_affichage_liste_bull', 'affichage_liste_bulletins_tableau', 'Fonction d\'affichage de la liste des bulletins d\'un périodique\nValeurs possibles:\naffichage_liste_bulletins_normale (Si paramètre vide)\naffichage_liste_bulletins_tableau\naffichage_liste_bulletins_depliable', 'e_aff_notice', 0),
(517, 'transferts', 'statut_validation', '0', 'id du statut dans lequel seront placés les documents dont le transfert est validé', '', 1),
(516, 'pmb', 'transferts_actif', '0', 'Active le systeme de transferts d\'exemplaires entre sites\n 0: Non \n 1: Oui', '', 0),
(479, 'dsi', 'bannette_notices_order', 'index_serie, tnvol, index_sew', 'Ordre des notices au sein de la bannette: \n index_serie, tnvol, index_sew : par titre \n create_date desc : par date de saisie décroissante \n rand() : aléatoire', '', 0),
(480, 'opac', 'websubscribe_show', '0', 'Afficher la possibilité de s\'inscrire en ligne ?\n0: Non\n1: Oui\n2: Oui + proposition s\'incription sur les réservations/abonnements', 'f_modules', 0),
(481, 'opac', 'websubscribe_empr_status', '2,1', 'Id des statuts des inscrits séparés par une virgule: en attente de validation, validés', 'f_modules', 0),
(482, 'opac', 'websubscribe_empr_categ', '0', 'Id de la catégorie des inscrits par le web non adhérents complets', 'f_modules', 0),
(483, 'opac', 'websubscribe_empr_stat', '0', 'Id du code statistique des inscrits par le web non adhérents complets', 'f_modules', 0),
(484, 'opac', 'websubscribe_valid_limit', '24', 'Durée maximum des inscriptions en attente de validation', 'f_modules', 0),
(485, 'pmb', 'mail_html_format', '1', 'Format d\'envoi des mails à partir de l\'opac: \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !', '', 0),
(486, 'opac', 'mail_html_format', '1', 'Format d\'envoi des mails à partir de l\'opac: \n 0: Texte brut\n 1: HTML \n 2: HTML, images incluses\nAttention, ne fonctionne qu\'en mode d\'envoi smtp !', 'a_general', 0),
(487, 'opac', 'websubscribe_empr_location', '0', 'Id de la localisation des inscrits par le web non adhérents complets', 'f_modules', 0),
(488, 'opac', 'allow_bannette_export', '0', 'Possibilité pour les lecteurs de recevoir les notices de leurs bannettes privées en pièce jointe au mail ?\n 0: Non \n 1: Oui', 'l_dsi', 0),
(489, 'opac', 'expl_data', 'expl_cb,expl_cote,tdoc_libelle,location_libelle,section_libelle', 'Colonne des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,tdoc_libelle,location_libelle,section_libelle', 'e_aff_notice', 0),
(490, 'opac', 'expl_order', 'location_libelle,section_libelle,expl_cote,tdoc_libelle', 'Ordre d\'affichage des exemplaires, dans l\'ordre donné, séparé par des virgules : location_libelle,section_libelle,expl_cote,tdoc_libelle', 'e_aff_notice', 0),
(491, 'opac', 'curl_available', '1', 'La librairie cURL est-elle disponible pour les interrogations RSS notamment ? \n 0: Non \n 1: Oui', 'a_general', 0),
(492, 'pmb', 'curl_available', '1', 'La librairie cURL est-elle disponible pour les interrogations RSS notamment ? \n 0: Non \n 1: Oui', '', 0),
(493, 'opac', 'thesaurus_defaut', '1', 'Identifiant du thésaurus par défaut.', 'i_categories', 0),
(494, 'opac', 'recherches_pliables', '1', 'Les cases à cocher de la recherche simple sont-elles pliées ? \r\n 0: Non \r\n 1: Oui et pliée par défaut \r\n 2: Oui et dépliée par défaut \r\n 3: invisibles', 'c_recherche', 0),
(495, 'pmb', 'rfid_ip_port', '192.168.0.10,SerialPort=10;', 'Association ip du poste de prêt et Numéro du port utilisé par le serveur RFID. Ex: 192.168.0.10,SerialPort=10; IpPosteClient,SerialPort=NumPortPlatine; séparé par des points-virgules pour désigner tous les postes', '', 0),
(496, 'pmb', 'pret_timeout_temp', '15', 'Temps en minutes, après lequel un prêt temporaire est effacé', '', 0),
(497, 'opac', 'permalink', '0', 'Afficher l\'Id de la notice avec un lien permanent ? \n 0: Non \n 1: Oui', 'e_aff_notice', 0),
(498, 'pdflettreretard', '3after_recouvrement', 'Sans nouvelles de votre part dans les sept jours, nous nous verrons contraints de déléguer au Trésor Public le recouvrement des ouvrages ci-dessus.', 'Texte apparaissant après la liste des ouvrages en recouvrement s\'il n\'y a pas d\'autres ouvrages en niveau 1 et 2', '', 0),
(499, 'pdflettreretard', 'impression_tri', 'empr_cp,empr_ville,empr_nom,empr_prenom', 'Tri pour l\'impression des lettres de relances ? Les champs sont ceux de la table empr séparés par des virgules. Exemple: empr_nom, empr_prenom', '', 0),
(500, 'pmb', 'pret_date_retour_adhesion_depassee', '0', 'La date de retour peut-elle dépasser la date de fin d\'adhésion ? \n 0: Non: la date de retour sera calculée pour ne pas dépasser la date de fin d\'adhésion. \n 1: Oui, la date de retour du prêt sera indépendante de date de fin d\'adhésion.', '', 0),
(501, 'opac', 'extended_search_auto', '1', 'En recherche multicritères, la sélection d\'un champ ajoute celui-ci automatiquement sans avoir besoin de cliquer sur le bouton Ajouter ? \n 0: Non \n 1: Oui', 'c_recherche', 0),
(502, 'pmb', 'extended_search_auto', '1', 'En recherche multicritères, la sélection d\'un champ ajoute celui-ci automatiquement sans avoir besoin de cliquer sur le bouton Ajouter ? \n 0: Non \n 1: Oui', '', 0),
(503, 'thesaurus', 'categories_affichage_ordre', '0', 'Paramétrage de l\'ordre d\'affichage des catégories d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1', 'categories', 0),
(504, 'opac', 'categories_affichage_ordre', '0', 'Paramétrage de l\'ordre d\'affichage des catégories d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1', 'i_categories', 0),
(505, 'pmb', 'rfid_driver', '', 'Driver du pilote RFID : le nom du répertoire contenant les javascripts propre au matériel en place.', '', 0),
(506, 'pmb', 'allow_external_search', '1', 'Autorisation ou non de la recherche par connecteurs externes (masque également le menu Administration-Connecteurs) \r\n 0 : Non \r\n 1 : Oui', '', 0),
(507, 'pmb', 'scan_pmbws_client_url', '', 'URL de l\'interface de numérisation (client du webservice)', '', 0),
(508, 'pmb', 'scan_pmbws_url', '', 'URL du webservice de pilotage du scanner', '', 0),
(509, 'opac', 'biblio_main_header', '<h3>Des services pour PMB</h3>', 'Texte pouvant apparaitre dans le bloc principal, au dessus de tous les autres, nécessaire pour certaines mises en page particulières.', 'b_aff_general', 0),
(510, 'opac', 'sugg_localises', '0', 'Activer la localisation des suggestions des lecteurs ? \n 0: Pas de localisation possible.\n 1: Localisation au choix du lecteur.\n 2: Localisation restreinte à la localisation du lecteur.', 'a_general', 0),
(511, 'acquisition', 'sugg_localises', '0', 'Activer la localisation des suggestions ? \n 0: Pas de localisation possible. \n 1: Localisation activée.', '', 0),
(512, 'opac', 'categories_nav_max_display', '200', 'Limiter l\'affichage des catégories en navigation dans les sous-catégories. 0: Pas de limitation. >0: Nombre max de catégories à afficher', 'i_categories', 0),
(513, 'pmb', 'pret_aff_limitation', '0', 'Activer la limitation de l\'affichage de la liste des prêts dans la fiche lecteur ? \n 0: Inactif. \n 1: Limitation activée', '', 0),
(514, 'pmb', 'pret_aff_nombre', '10', 'Nombre de prêts à afficher si le paramètre pret_aff_limitation est actif. \n 0: tout voir, illimité. \n ## Nombre de prêts à afficher sur la première page', '', 0),
(515, 'pmb', 'printer_ticket_url', '', 'Permet d\'utiliser une imprimante de ticket, connectée en local sur le poste de prêt client. Vide : pas d\'imprimante. Url (http://localhost/printer/bixolon_srp350.php ) : imprimante active.', '', 0),
(518, 'transferts', 'statut_transferts', '0', 'id du statut dans lequel seront placés les documents en cours de transit', '', 1),
(519, 'transferts', 'validation_actif', '1', 'Active la validation des transferts\n 0: Non \n 1: Oui', '', 1),
(520, 'transferts', 'nb_jours_pret_defaut', '30', 'Nombre de jours de pret par defaut', '', 1),
(521, 'transferts', 'nb_jours_alerte', '7', 'Nombre de jours avant la fin du pret ou l\'alerte s\'affiche', '', 1),
(522, 'transferts', 'transfert_transfere_actif', '0', 'Autorise le transfert d\'exemplaire deja transferer', '', 1),
(523, 'transferts', 'tableau_nb_lignes', '10', 'Nombre de transferts affichés dans les tableaux', '', 1),
(524, 'transferts', 'envoi_lot', '0', 'traitement par lot possible en envoi', '', 1),
(525, 'transferts', 'reception_lot', '0', 'traitement par lot possible en reception', '', 1),
(526, 'transferts', 'retour_lot', '0', 'traitement par lot possible en retour', '', 1),
(527, 'transferts', 'retour_origine', '0', 'Force le retour de l\'exemplaire dans son lieu d\'origine\n 0: Non \n 1: Oui', '', 1),
(528, 'transferts', 'retour_origine_force', '1', 'Permet de forcer le retour de l\'exemplaire\n 0: Non \n 1: Oui', '', 1),
(529, 'transferts', 'retour_action_defaut', '1', 'Action par defaut lors du retour d\'un emprunt\n 0: change localisation \n 1: genere transfert', '', 1),
(530, 'transferts', 'retour_action_autorise_autre', '1', 'Autorise une autre action lors du retour de l\'exemplaire\n 0: Non\n 1: Oui', '', 1),
(531, 'transferts', 'retour_change_localisation', '1', 'Sauvegarde de la localisation lors du changement\n 0: Non \n 1: Oui', '', 1),
(532, 'transferts', 'retour_etat_transfert', '1', 'Etat du transfert lors de sa generation auto\n 0: creer \n 1: envoyer', '', 1),
(533, 'transferts', 'retour_motif_transfert', 'Transfert suite au retour de l\'exemplaire sur notre site', 'Motif du transfert lors de sa generation auto', '', 1),
(534, 'transferts', 'choix_lieu_opac', '0', '0 pour pas de choix et obligatoirement dans la localisation ou est enregistré l\'utilisateur, 1 pour n\'importe quelle localisation au choix, 2 pour un lieu fixe précisé, 3 pour le lieu de l\'exemplaire', '', 1),
(535, 'transferts', 'site_fixe', '1', 'id du site pour le retrait des livres si choix_lieu_opac=2', '', 1),
(536, 'transferts', 'resa_motif_transfert', 'Transfert suite à une réservation', 'Motif du transfert lors de sa generation auto pour une réservation', '', 1),
(537, 'transferts', 'resa_etat_transfert', '1', 'Etat du transfert lors de sa generation auto\n 0: creer \n 1: envoyer', '', 1),
(538, 'pmb', 'recherche_ajax_mode', '1', 'Affichage accéléré des résultats de recherche: \"réduit\" uniquement, la suite est chargée lors du click sur le \"+\". \n 0: Inactif \n 1: Actif', '', 0),
(539, 'pmb', 'expl_title_display_format', 'expl_location,expl_section,expl_cote,expl_cb', 'Format d\'affichage du titre de l\'exemplaire en recherche multi-critères d\'exemplaires. Les libellés des champs correspondent aux champs de la table exemplaires, ou aux id de champs personnalisés. Séparés par une virgule. Les champs disposant d\'un libellé seront remplacés par le libellé correspondant. Exemple: expl_location,expl_section,expl_cote,expl_cb', '', 0),
(540, 'opac', 'empr_code_info', '', 'Code HTML affiché au dessus des boutons dans la fiche emprunteur.', 'a_general', 0),
(541, 'opac', 'term_search_height_bottom', '120', 'Hauteur de la partie supérieure de la frame de recherche par termes (en px)', 'c_recherche', 0),
(542, 'pmb', 'rfid_library_code', '', 'Code numérique d\'identification de la bibliothèque propriétaire des exemplaires (10 caractères)', '', 0),
(543, 'opac', 'show_infopages_id', '5', 'Id des infopages à afficher sous la recherche simple, séparées par des virgules.', 'f_modules', 0),
(544, 'thesaurus', 'auto_postage_montant', '0', 'Activer la recherche des notices des catégories mères ? \n  0 non, \n 1 oui', 'i_categories', 0),
(545, 'thesaurus', 'auto_postage_descendant', '0', 'Activer la recherche des notices des catégories filles. \n 0 non, \n 1 oui', 'i_categories', 0),
(546, 'thesaurus', 'auto_postage_nb_descendant', '0', 'Nombre de niveaux de recherche de notices dans les catégories filles. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0),
(547, 'thesaurus', 'auto_postage_nb_montant', '0', 'Nombre de niveaux de recherche de notices dans les catégories mères. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0),
(548, 'thesaurus', 'auto_postage_etendre_recherche', '0', 'Proposer la possibilité d\'étendre la recherche dans les catégories mères ou filles. \n 0: non, \n 1: Exclusivement dans les catégories filles, \n 2: Etendre dans les catégories mères et filles, \n 3: Exclusivement dans les catégories mères. ', 'i_categories', 0),
(549, 'opac', 'auto_postage_montant', '0', 'Activer la recherche des notices des catégories mères. \n 0 non, \n 1 oui', 'i_categories', 0),
(550, 'opac', 'auto_postage_descendant', '0', 'Activer la recherche des notices des catégories filles. \n 0 non, \n 1 oui', 'i_categories', 0),
(551, 'opac', 'auto_postage_nb_descendant', '0', 'Nombre de niveaux de recherche de notices dans les catégories filles. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0),
(552, 'opac', 'auto_postage_nb_montant', '0', 'Nombre de niveaux de recherche de notices dans les catégories mères. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0),
(553, 'opac', 'auto_postage_etendre_recherche', '0', 'Proposer la possibilité d\'étendre la recherche dans les catégories mères ou filles. \n 0: non, \n 1: Exclusivement dans les catégories filles, \n 2: Etendre dans les catégories mères et filles, \n 3: Exclusivement dans les catégories mères. ', 'i_categories', 0),
(554, 'gestion_acces', 'active', '0', 'Module gestion des droits d\'accès activé ?\n 0 : Non.\n 1 : Oui.', '', 0),
(555, 'gestion_acces', 'user_notice', '0', 'Gestion des droits d\'accès des utilisateurs aux notices \n 0 : Non.\n 1 : Oui.', '', 0),
(556, 'pmb', 'abt_end_delay', '30', 'Délais d\'alerte d\'avertissement des abonnements arrivant à échéance (en jours)', '', 0),
(557, 'pmb', 'set_time_limit', '1200', 'max_execution_time de certaines opérations (export d\'actions personnalisées, envoi DSI, export, etc.) \nAttention, peut être sans effet si l\'hébergement ne l\'autorise pas (free.fr par exemple)\n 0 : illimité (déconseillé) \n ###: ### secondes', '', 0),
(558, 'pmb', 'expl_list_display_comments', '3', 'Afficher les commentaires des exemplaires en liste d\'exemplaires : \r\n 0 : non \r\n 1 : commentaire bloquant \r\n 2 : commentaire non bloquant \r\n 3 : les deux commentaires', '', 0),
(559, 'pmb', 'confirm_delete_from_caddie', '2', 'Action à réaliser lors de la suppression d\'une notice située dans un panier. \r\n0 : Interdire \r\n1 : Supprimer sans confirmation \r\n2 : Demander une confirmation de suppression ', '', 0),
(560, 'opac', 'flux_rss_notices_order', ' index_serie, tnvol, index_sew ', 'Ordre d\'affichage des notices dans les flux sortants dans l\'opac \n  index_serie, tnvol, index_sew : tri par titre de série et titre \n rand()  : aléatoire \n notice_id desc par ordre décroissant de création de notice', 'l_dsi', 0),
(561, 'opac', 'modules_search_titre_uniforme', '1', 'Recherche simple dans les titres uniformes :\n 0 : interdite\n 1 : autorisée\n 2 : autorisée et validée par défaut\n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(562, 'opac', 'congres_affichage_mode', '0', 'Mode d\'affichage des congrès: \n 0 : Comme pour les auteurs, \n 1 : ajout d\'un navigateur de congrès', 'd_aff_recherche', 0),
(563, 'opac', 'show_suggest_notice', '0', 'Afficher le lien de proposition de suggestion sur une notice existante.\n 0 : Non.\n 1 : Oui, avec authentification.\n 2 : Oui, sans authentification.', 'f_modules', 0),
(564, 'pmb', 'explnum_statut', '0', 'Utiliser un statut sur les documents numériques \n 0: non \n 1: oui', '', 0),
(565, 'opac', 'show_empty_items_block', '1', 'Afficher le bloc exemplaires même si aucun exemplaire sur la notice ? : \n 0 : Non, \n 1 : Oui', 'd_aff_recherche', 0),
(566, 'pmb', 'printer_ticket_script', '', 'Script permettant de personaliser l\'impression du ticket de prêt. Le répertoire du script est à paramétrer à partir de la racine de PMB.\nSi vide PMB utilise ./circ/ticket-pret.inc.php', '', 0),
(567, 'opac', 'curl_proxy', '', 'Paramétrage de proxy de cURL, vide si aucun proxy, sinon\nhost,port,user,password;2nd_host et ainsi de suite', 'a_general', 0),
(568, 'pmb', 'curl_proxy', '', 'Paramétrage de proxy de cURL, vide si aucun proxy, sinon\nhost,port,user,password;2nd_host et ainsi de suite', '', 0),
(570, 'opac', 'password_forgotten_show', '1', 'Afficher le lien  \"Mot de passe oublié ?\" \n 0: Non \n 1: Oui', 'f_modules', 0),
(571, 'opac', 'aff_expl_localises', '0', 'Activer l\'affichage des exemplaires localisés par onglet.\n 0 : désactivé \n 1: premier onglet affiche les exemplaires de la localisation du lecteur, le deuxieme affiche tous les exemplaires', 'e_aff_notice', 0),
(572, 'gestion_acces', 'empr_notice', '0', 'Gestion des droits d\'accès des emprunteurs aux notices \n 0 : Non.\n 1 : Oui.', '', 0),
(573, 'opac', 'show_infopages_id_top', '', 'Id des infopages à afficher SUR la recherche simple, séparées par des virgules.', 'f_modules', 0),
(574, 'opac', 'show_search_title', '0', 'Afficher le titre du bloc de recherche : \n 0 : Non, \n 1 : Oui', 'f_modules', 0),
(575, 'opac', 'allow_personal_search', '0', 'Activer l\'affichage de l\'onglet des recherches personalisées \n 0 : Non.\n 1 : Oui.', 'c_recherche', 0),
(576, 'ldap', 'opac_only', '0', 'Ne pas utiliser l\'authentification LDAP en gestion: \n 0: Non \n 1 : Oui, en OPAC uniquement', '', 0),
(577, 'pmb', 'multi_search_operator', 'or', 'Type d\'opérateur de recherche pour les listes avec plusieurs valeurs: \n or : pour le OU \n and : pour le ET', '', 0),
(578, 'opac', 'multi_search_operator', 'or', 'Type d\'opérateur de recherche pour les listes avec plusieurs valeurs: \n or : pour le OU \n and : pour le ET', 'c_recherche', 0),
(579, 'transferts', 'pret_statut_transfert', '0', 'Autoriser le prêt lorsque l\'exemplaire est en transfert', '', 1),
(580, 'exportparam', 'generer_liens', '0', 'Générer les liens entre les notices pour l\'export', '', 1),
(581, 'exportparam', 'export_mere', '0', 'Exporter les notices liées mères', '', 1),
(582, 'exportparam', 'export_fille', '0', 'Exporter les notices liées filles', '', 1),
(583, 'exportparam', 'export_bull_link', '1', 'Exporter les liens vers les bulletins pour les notices d\'article', '', 1),
(584, 'exportparam', 'export_perio_link', '1', 'Exporter les liens vers les périodiques pour les notices d\'article', '', 1),
(585, 'exportparam', 'export_art_link', '1', 'Exporter les liens vers les articles pour les notices de périodique', '', 1),
(586, 'exportparam', 'export_bulletinage', '0', 'Générer le bulletinage pour les notices de périodiques', '', 1),
(587, 'exportparam', 'export_notice_perio_link', '0', 'Exporter les notices liées de périodique', '', 1),
(588, 'exportparam', 'export_notice_art_link', '0', 'Exporter les notices liées d\'article', '', 1),
(589, 'exportparam', 'export_notice_mere_link', '0', 'Exporter les notices mères liées', '', 1),
(590, 'exportparam', 'export_notice_fille_link', '0', 'Exporter les notices filles liées', '', 1),
(591, 'opac', 'exp_generer_liens', '0', 'Générer les liens entre les notices pour l\'export', '', 1),
(592, 'opac', 'exp_export_mere', '0', 'Exporter les notices liées mères', '', 1),
(593, 'opac', 'exp_export_fille', '0', 'Exporter les notices liées filles', '', 1),
(594, 'opac', 'exp_export_bull_link', '1', 'Exporter les liens vers les bulletins pour les notices d\'article', '', 1),
(595, 'opac', 'exp_export_perio_link', '1', 'Exporter les liens vers les périodiques pour les notices d\'article', '', 1),
(596, 'opac', 'exp_export_art_link', '1', 'Exporter les liens vers les articles pour les notices de périodique', '', 1),
(597, 'opac', 'exp_export_bulletinage', '0', 'Générer le bulletinage pour les notices de périodiques', '', 1),
(598, 'opac', 'exp_export_notice_perio_link', '0', 'Exporter les notices liées de périodique', '', 1),
(599, 'opac', 'exp_export_notice_art_link', '0', 'Exporter les notices liées d\'article', '', 1),
(600, 'opac', 'exp_export_notice_mere_link', '0', 'Exporter les notices mères liées', '', 1),
(601, 'opac', 'exp_export_notice_fille_link', '0', 'Exporter les notices filles liées', '', 1),
(602, 'pmb', 'perio_vidage_log', '1', 'Périodicité de transfert des données depuis la table temporaire des logs vers la table de stockage  (en jours).', '', 0),
(603, 'pmb', 'perio_vidage_stat', '2,15', 'Périodicité de vidage de la table de stockage (mode,jour) : \r\n0 : ne rien faire\r\n1,x : vider tous les x jours\r\n2,x : vider tous ce qui à plus de x jours', '', 0),
(604, 'pmb', 'logs_activate', '0', 'Activer les statistiques pour l\'OPAC: \n 0 : non activé \n 1 : activé', '', 0),
(605, 'opac', 'shared_lists', '0', 'Activer les listes de lecture partagées \n 0 : non activées \n 1 : activées', 'a_general', 0),
(606, 'pmb', 'indexation_docnum', '0', 'Activer l\'indexation des documents numériques \n 0 : non activée \n 1 : activée', '', 0),
(607, 'pmb', 'indexation_docnum_allfields', '0', 'Activer par défaut la recherche dans les documents numériques pour la recherche \"Tous les champs\" \n 0 : non activée \n 1 : activée', '', 0),
(608, 'opac', 'indexation_docnum_allfields', '0', 'Activer par défaut la recherche dans les documents numériques pour la recherche \"Tous les champs\" \n 0 : non activée \n 1 : activée', 'c_recherche', 0),
(609, 'opac', 'modules_search_docnum', '0', 'Recherche simple dans les documents numériques \n 0 : interdite \n 1 : autorisée \n 2 : autorisée et validée par défault', 'c_recherche', 0),
(610, 'pmb', 'location_reservation', '0', 'Utiliser la gestion de la réservation localisée?\n 0: Non\n 1: Oui', '', 0),
(611, 'pmb', 'extension_tab', '0', 'Afficher l\'onglet Extension ? \n 0 : Non \n 1 : Oui', '', 0),
(612, 'pmb', 'indexation_docnum_default', '0', 'Indexer le document numérique par défaut ? \n 0 : Non \n 1 : Oui', '', 0),
(613, 'opac', 'shared_lists_readonly', '0', 'Listes de lecture partagées en lecture seule \n 0 : non activées \n 1 : activées', 'a_general', 0),
(614, 'opac', 'connexion_phrase', '', 'Phrase permettant l\'encodage de la connexion automatique à partir d\'un mail', 'a_general', 0),
(615, 'pmb', 'afficher_numero_lecteur_lettres', '1', 'Afficher le numéro et le mail du lecteur sous l\'adresse dans les différentes lettres', '', 0),
(616, 'pmb', 'lettres_bloc_adresse_position_absolue', '0 100 40', 'Place le bloc d\'adresse selon des coordonnées absolues.\nactivé x y\nactivé : activer cette fonction (valeurs: 0/1)\nx : Position horizontale\ny : Position verticale', '', 0),
(617, 'pmb', 'external_service_search_cache', '3600', 'Durée de vie des recherches dans le cache, pour les services externes, en secondes.', '', 0),
(618, 'pmb', 'external_service_session_duration', '600', 'Durée de vie des sessions pour les services externes, en secondes.', '', 0),
(619, 'opac', 'allow_multiple_sugg', '0', 'Autoriser les suggestions multiples.\r\n0: non\r\n1: oui', 'a_general', 0),
(620, 'dsi', 'bannette_notices_template', '0', 'Id du template de notice utilisé par défaut en diffusion de bannettes. Si vide ou à 0, le template classique est utilisé.', '', 0),
(621, 'demandes', 'active', '0', 'Module demandes activé.\n 0 : Non.\n 1 : Oui.', '', 0),
(622, 'demandes', 'statut_notice', '0', 'Id du statut de notice pour la notice de demandes.', '', 0),
(623, 'opac', 'demandes_active', '0', 'Activer les demandes pour l\'OPAC.\n 0 : Non.\n 1 : Oui.', 'a_general', 0),
(624, 'pmb', 'use_uniform_title', '1', 'Utiliser les titres uniformes ? \r\n 0 : Non.\r\n 1 : Oui.', '', 0),
(625, 'opac', 'print_expl_default', '0', 'En impression de panier, imprimer les exemplaires est coché par défaut \n 0 : Non \n 1 : Oui', 'a_general', 0),
(626, 'demandes', 'include_note', '0', 'Inclure automatiquement les notes dans le rapport documentaire.', '', 0),
(627, 'opac', 'ie_reload_on_resize', '0', 'Recharger la page si l\'utilisateur redimensionne son navigateur (pb de CSS avec IE) ? \n 0: Non \n 1: Oui', 'a_general', 0);
INSERT INTO `parametres` VALUES
(628, 'pmb', 'expl_show_dates', '0', 'Afficher les dates des exemplaires ? \n 0 : Aucune date.\n 1 : Date de création et modification.\n 2 : Date de dépôt et retour (BDP).\n 3 : Date de création, modification, dépôt et retour.', '', 0),
(629, 'gestion_acces', 'user_notice_def', '0', 'Valeur par défaut en modification de notice pour les droits d\'accès utilisateurs - notices \n 0 : Recalculer.\n 1 : Choisir.', '', 0),
(630, 'gestion_acces', 'empr_notice_def', '0', 'Valeur par défaut en modification de notice pour les droits d\'accès emprunteurs - notices \n 0 : Recalculer.\n 1 : Choisir.', '', 0),
(631, 'opac', 'show_exemplaires_analysis', '0', 'Afficher les exemplaires du bulletin sous l\'article affiché ? \n 0: Non \n 1: Oui', 'e_aff_notice', 0),
(632, 'pmb', 'show_notice_id', '0', 'Afficher l\'identifiant de la notice dans le descriptif ? \n 0 : Non.\n 1 : Oui. \n 1,X : Oui avec préfixe X', '', 0),
(633, 'opac', 'section_notices_order', ' index_serie, tnvol, index_sew ', 'Ordre d\'affichage des notices dans les sections dans l\'opac \n  index_serie, tnvol, index_sew : tri par titre de série et titre ', 'k_section', 0),
(634, 'opac', 'show_onglet_help', '0', 'Afficher l\'onglet HELP avec les onglets de recherche affichant l\'infopage et un lien vers l\'infopage dans la barre de navigation \n 0 : Non.\n ## : id de l\'infopage. \n', 'f_modules', 0),
(635, 'opac', 'navig_empr', '0', 'Afficher l\'onglet \"Votre compte\" dans la barre de navigation de l\'Opac ? \n 0 : Non \n 1 : Oui', 'a_general', 0),
(637, 'pmb', 'prefill_cote_ajax', '', 'Script personnalisé de construction de la cote de l\'exemplaire en ajax', '', 0),
(638, 'pmb', 'hide_biblioinfo_letter', '0', 'Masquer les informations de localisation dans l\'entête des lettres (pour les bibliothèques possédant du papier à entête)', '', 0),
(639, 'pmb', 'lettres_code_mail_position_absolue', '0 100 6', 'Placer le code lecteur et le mail selon des coordonnées absolues.\n activé x y \n activé : activer cette fonction (valeurs: 0/1) \n x : Position horizontale \n y : Position verticale', '', 0),
(640, 'opac', 'adhesion_expired_status', '0', 'Id du statut permettant de restreindre les droits des emprunteurs dont l\'abonnement est dépassé. \n\rPMB fera un AND logique avec les droits d\'origine.', 'a_general', 0),
(641, 'pmb', 'resa_retour_action_defaut', '1', 'Définit l\'action par défaut à effectuer lors d\'un retour si le document est réservé.\r\n0, A traiter plus tard.\r\n1, Valider la réservation.', '', 0),
(642, 'pmb', 'notice_fille_format', '0', 'Affichage des notices filles \n 0: avec leurs détails (notice dépliable avec un plus) \n 1: Juste l\'entête', '', 0),
(643, 'pmb', 'hide_retdoc_loc_error', '0', 'Gestion du retour de prêt d\'un document issu d\'une autre localisation:\n 0 : Rendu, sans message d\'erreur\n 1 : Non rendu, avec message d\'erreur\n 2 : Rendu, avec message d\'erreur', '', 0),
(644, 'pmb', 'selfservice_allow', '0', 'Activer de la gestion de la borne de prêt?\n0 : Non. \n1 : Oui.', '', 0),
(645, 'selfservice', 'loc_autre_todo', '0', 'Action à effectuer si le document est issu d\'une autre localisation', '', 1),
(646, 'selfservice', 'loc_autre_todo_msg', '', 'Message si le document est réservé sur une autre localisation', '', 1),
(647, 'selfservice', 'resa_ici_todo', '0', 'Action à effectuer si le document est réservé sur cette localisation', '', 1),
(648, 'selfservice', 'resa_ici_todo_msg', '', 'Message si le document est réservé sur cette localisation', '', 1),
(649, 'selfservice', 'resa_loc_todo', '0', 'Action à effectuer si le document est réservé sur une autre localisation', '', 1),
(650, 'selfservice', 'resa_loc_todo_msg', '', 'Message si le document est réservé sur une autre localisation', '', 1),
(651, 'selfservice', 'retour_retard_msg', '', 'Message si le document est rendu en retard', '', 1),
(652, 'selfservice', 'retour_blocage_msg', '', 'Message si le document est rendu en retard avec blocage', '', 1),
(653, 'selfservice', 'retour_amende_msg', '', 'Message si le document est rendu en retard avec amende', '', 1),
(654, 'selfservice', 'pret_carte_invalide_msg', 'Votre carte n\'est pas valide !', 'Message borne de prêt: Votre carte n\'est pas valide !', '', 1),
(655, 'selfservice', 'pret_pret_interdit_msg', 'Vous n\'êtes pas autorisé à emprunter !', 'Message borne de prêt: Vous n\'êtes pas autorisé à emprunter !', '', 1),
(656, 'selfservice', 'pret_deja_prete_msg', 'Document déjà prêté ! allez le signaler !', 'Message borne de prêt: Document déjà prêté ! allez le signaler !', '', 1),
(657, 'selfservice', 'pret_deja_reserve_msg', 'Vous ne pouvez pas emprunter ce document', 'Message borne de prêt: Vous ne pouvez pas emprunter ce document', '', 1),
(658, 'selfservice', 'pret_quota_bloc_msg', 'Vous ne pouvez pas emprunter ce document', 'Message borne de prêt: Vous ne pouvez pas emprunter ce document', '', 1),
(659, 'selfservice', 'pret_non_pretable_msg', 'Ce document n\'est pas prêtable', 'Message borne de prêt: Ce document n\'est pas prêtable', '', 1),
(660, 'selfservice', 'pret_expl_inconnu_msg', 'Ce document est inconnu', 'Message borne de prêt: Ce document est inconnu', '', 1),
(661, 'selfservice', 'pret_prolonge_non_msg', 'Le prêt ne peut être prolongé', 'Message borne de prêt: Le prêt ne peut être prolongé', '', 1),
(675, 'opac', 'visionneuse_params', '', 'tableau de correspondance mimetype=>class', 'm_photo', 1),
(676, 'opac', 'allow_self_checkout', '0', 'Proposer de faire du prêt autonome dans l\'OPAC.\n 0 : Non.\n 1 : Autorise le prêt de document.\n 2 : Autorise le retour de document.\n 3 : Autorise le prêt et le retour de document.\n', 'a_general', 0),
(663, 'opac', 'photo_filtre_mimetype', '', 'Liste des mimetypes utilisés pour l\'affichage des résultats en mode photothèque séparés par une virgule et entre cotes (ex:\'application/pdf\',\'image/png\')', 'm_photo', 0),
(664, 'empr', 'sms_activation', '0,0,0,0', 'Activation de l\'envoi de sms. : relance 1,relance 2,relance 3,resa\n\n 0: Inactif\n 1: Actif', '', 0),
(665, 'empr', 'sms_config', '', 'Paramétrage de l\'envoi de sms. \nUsage:\n class_name=nom_de_la_classe;param_connection;\nExemple:\n class_name=smstrend;login=xxxx@sigb.net;password=xxxx;tpoa=xxxx;', '', 0),
(666, 'empr', 'sms_msg_resa_dispo', 'Bonjour,\nUn document réservé est disponible.\nConsultez votre compte!', 'Texte du sms envoyé lors de la validation d\'une réservation', '', 0),
(667, 'empr', 'sms_msg_resa_suppr', 'Bonjour,\nUne réservation est supprimée.\nConsultez votre compte!', 'Texte du sms envoyé lors de la suppression d\'une réservation', '', 0),
(668, 'empr', 'sms_msg_retard', 'Bonjour,\nVous avez un ou plusieurs document(s) en retard.\nConsultez votre compte!', 'Texte du sms envoyé lors d\'un retard', '', 0),
(669, 'pmb', 'procedure_server_address', '', 'Adresse du serveur de procédures distantes.', '', 0),
(670, 'pmb', 'procedure_server_credentials', '', 'Authentification sur le serveur de procédures distantes.\n1ère ligne: email\n2ème ligne: mot de passe.', '', 0),
(671, 'pmb', 'rfid_pret_mode', '0', 'Mode de fonctionnement du prêt:\n 0: Un document retiré de la platine est retiré du prêt.\n 1: Un document retiré de la platine est conservé pour faciliter le prêt de nombreux documents. ', '', 0),
(1042, 'pmb', 'img_cache_folder', '', 'Répertoire de stockage du cache des images', '', 0),
(673, 'fiches', 'active', '0', 'Module \'fiches\' activé.\n 0 : Non.\n 1 : Oui.', '', 0),
(674, 'opac', 'visionneuse_allow', '0', 'Visionneuse activée.\n 0 : Non.\n 1 : Oui.', 'm_photo', 0),
(677, 'opac', 'self_checkout_url_connector', '', 'URL du connecteur en gestion permettant d\'effectuer le prêt autonome.', 'a_general', 0),
(678, 'finance', 'recouvrement_lecteur_statut', '0', 'Mémorise le statut que prennent les lecteurs lors du passage en recouvrememnt', '', 1),
(679, 'thesaurus', 'auto_postage_search', '0', 'Activer l\'indexation des catégories mères et filles pour la recherche de notices. \n 0 non, \n 1 oui', 'i_categories', 0),
(680, 'thesaurus', 'auto_postage_search_nb_descendant', '0', 'Nombre de niveaux de recherche de notices dans les catégories filles. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0),
(681, 'thesaurus', 'auto_postage_search_nb_montant', '0', 'Nombre de niveaux de recherche de notices dans les catégories mères. \n *: illimité, \n n: nombre de niveaux', 'i_categories', 0),
(682, 'opac', 'show_bulletin_nav', '1', 'Affichage d\'un navigateur dans les bulletins d\'un périodique. \r\n 0 non \r\n 1 oui', 'f_modules', 0),
(683, 'pmb', 'play_pret_sound', '1', 'Jouer l\'alerte sonore si le prêt et le retour se passe sans erreur ? \n 0 : Non.\n 1 : Oui.', '', 0),
(684, 'pmb', 'catalog_verif_js', '', 'Script de vérification de saisie de notice', '', 0),
(685, 'opac', 'default_style_addon', '', 'Ajout de styles CSS aux feuilles déjà incluses ?\r\n Ne mettre que le code CSS, exemple:  body {background-color: #FF0000;}', 'a_general', 0),
(686, 'pmb', 'rfid_gates_server_url', '', 'URL du serveur des portiques RFID', '', 0),
(687, 'pmb', 'perso_sep', '/', 'Séparateur des valeurs de champ perso, espace ou ; ou , ou ...', '', 0),
(688, 'pmb', 'search_full_text', '0', 'Utiliser un index MySQL FULLTEXT pour la recherche sur les documents numériques \n 0: Non \n 1: Oui', '', 0),
(689, 'opac', 'parse_html', '0', 'Activer le parse HTML des pages OPAC \n 0: Non \n 1: Oui\n<a href=\'../../includes/interpreter/doc?group=inhtml\' target=\'_blank\'>Consulter la liste des fonctions disponibles</a>', 'a_general', 0),
(690, 'opac', 'notice_enrichment', '0', 'Activer l\'enrichissement des notices\r\n 0: Non \r\n 1: Oui', 'e_aff_notice', 0),
(691, 'opac', 'show_social_network', '0', 'Activer les partages sur les réseaux sociaux \r\n 0: Non \r\n 1: Oui', 'e_aff_notice', 0),
(692, 'pmb', 'opac_view_activate', '0', 'Activer les vues OPAC :\n 0 : non activé\n 1 : activé avec gestion classique\n 2 : activé avec gestion avancée', '', 0),
(693, 'opac', 'opac_view_activate', '0', 'Activer les vues OPAC:\n 0 : non activé \n 1 : activé', 'a_general', 0),
(694, 'pmb', 'sur_location_activate', '0', 'Activer les sur-localisations:\n 0 : non activé \n 1 : activé', '', 0),
(695, 'opac', 'sur_location_activate', '0', 'Activer les sur-localisations:\n 0 : non activé \n 1 : activé', 'a_general', 0),
(696, 'pmb', 'opac_view_class', '', 'Nom de la classe substituant la class opac_view pour la personnalisation de la gestion des vues Opac', '', 0),
(697, 'opac', 'faviconurl', '', 'URL du favicon, si vide favicon=celui de PMB', 'a_general', 0),
(698, 'opac', 'allow_affiliate_search', '0', 'Activer les recherches affiliées en OPAC:\n 0 : non \n 1 : oui', 'c_recherche', 0),
(699, 'acquisition', 'pdfrel_format_page', '210x297', 'Largeur x Hauteur de la page en mm', 'pdfrel', 0),
(700, 'acquisition', 'pdfrel_orient_page', 'P', 'Orientation de la page: P=Portrait, L=Paysage', 'pdfrel', 0),
(701, 'acquisition', 'pdfrel_marges_page', '10,20,10,10', 'Marges de page en mm : Haut,Bas,Droite,Gauche', 'pdfrel', 0),
(702, 'acquisition', 'pdfrel_pos_logo', '10,10,20,20', 'Position du logo: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur', 'pdfrel', 0),
(703, 'acquisition', 'pdfrel_pos_raison', '35,10,100,10,16', 'Position Raison sociale: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfrel', 0),
(704, 'acquisition', 'pdfrel_pos_date', '170,10,0,6,8', 'Position Date: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfrel', 0),
(705, 'acquisition', 'pdfrel_pos_adr_rel', '10,35,60,5,10', 'Position Adresse de relance: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfrel', 0),
(706, 'acquisition', 'pdfrel_pos_adr_fou', '100,55,100,6,14', 'Position Adresse fournisseur: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfrel', 0),
(707, 'acquisition', 'pdfrel_pos_num_cli', '10,80,0,10,16', 'Position numéro de client: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfrel', 0),
(708, 'acquisition', 'pdfrel_pos_num', '10,0,10,16', 'Position numéro de commande/devis: Distance par rapport au bord gauche de la page,Largeur,Hauteur,Taille police', 'pdfrel', 0),
(709, 'acquisition', 'pdfrel_text_size', '10', 'Taille de la police texte', 'pdfrel', 0),
(710, 'acquisition', 'pdfrel_pos_titre', '10,90,100,10,16', 'Position titre: Distance par rapport au bord gauche de la page,Distance par rapport au haut de la page,Largeur,Hauteur,Taille police', 'pdfrel', 0),
(711, 'acquisition', 'pdfrel_text_before', '', 'Texte avant le tableau de relances', 'pdfrel', 0),
(712, 'acquisition', 'pdfrel_text_after', '', 'Texte après le tableau de relances', 'pdfrel', 0),
(713, 'acquisition', 'pdfrel_tab_rel', '5,10', 'Tableau de relances: Hauteur ligne,Taille police', 'pdfrel', 0),
(714, 'acquisition', 'pdfrel_pos_footer', '15,8', 'Position bas de page: Distance par rapport au bas de page, Taille police', 'pdfrel', 0),
(715, 'acquisition', 'pdfrel_pos_sign', '10,60,5,10', 'Position signature: Distance par rapport au bord gauche de la page, Largeur, Hauteur ligne,Taille police', 'pdfrel', 0),
(716, 'acquisition', 'pdfrel_text_sign', 'Le responsable de la bibliothèque.', 'Texte signature', 'pdfrel', 0),
(717, 'acquisition', 'pdfrel_by_mail', '1', 'Effectuer les relances par mail :\n 0 : non \n 1 : oui', 'pdfrel', 0),
(718, 'acquisition', 'pdfrel_text_mail', 'Bonjour, \r\n\r\nVous trouverez ci-joint un état des commandes en cours.\r\n\r\nMerci de nous préciser par retour vos délais d\'envoi.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.', 'Texte du mail', 'pdfrel', 0),
(719, 'opac', 'show_perio_browser', '0', 'Affichage du navigateur de périodiques en page d\'accueil OPAC.\n 0 : Non.\n 1 : Oui.', 'f_modules', 0),
(720, 'acquisition', 'pdfrel_pdfrtf', '0', 'Envoi des relances en :\n 0 : pdf\n 1 : rtf', 'pdfrel', 0),
(721, 'opac', 'show_onglet_perio_a2z', '0', 'Activer l\'onglet du navigateur de périodiques en OPAC.\n 0 : Non.\n 1 : Oui.', 'c_recherche', 0),
(722, 'opac', 'avis_note_display_mode', '1', 'Mode d\'affichage de la note pour les avis de notices.\n 0 : Note non visible.\n 1 : Affichage de la note sous la forme d\'étoiles.\n 2 : Affichage de la note sous la forme textuelle.\n 3 : Affichage de la note sous la forme textuelle et d\'étoiles.\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.', 'a_general', 0),
(723, 'opac', 'avis_display_mode', '0', 'Mode d\'affichage des avis de notices.\n 0 : Visible en lien à coté de l\'onglet Public/ISBD de la notice.\n 1 : Visible dans la notice.', 'a_general', 0),
(724, 'pmb', 'planificateur_allow', '0', 'Planificateur activé.\n 0 : Non.\n 1 : Oui.', '', 0),
(725, 'acquisition', 'pdfrel_obj_mail', 'Etat des en-cours', 'Objet du mail', 'pdfrel', 0),
(726, 'acquisition', 'pdfcde_by_mail', '1', 'Effectuer les envois de commandes par mail :\n 0 : non \n 1 : oui', 'pdfcde', 0),
(727, 'acquisition', 'pdfcde_obj_mail', 'Commande', 'Objet du mail', 'pdfcde', 0),
(728, 'acquisition', 'pdfcde_text_mail', 'Bonjour, \r\n\r\nVous trouverez ci-joint une commande à traiter.\r\n\r\nMerci de nous confirmer par retour vos délais d\'envoi.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.', 'Texte du mail', 'pdfcde', 0),
(729, 'acquisition', 'pdfdev_by_mail', '1', 'Effectuer les envois de demandes de devis par mail :\n 0 : non \n 1 : oui', 'pdfdev', 0),
(730, 'acquisition', 'pdfdev_obj_mail', 'Demande de devis', 'Objet du mail', 'pdfdev', 0),
(731, 'acquisition', 'pdfdev_text_mail', 'Bonjour, \r\n\r\nVous trouverez ci-joint une demande de devis.\r\n\r\nCordialement,\r\n\r\nLe responsable de la bibliothèque.', 'Texte du mail', 'pdfdev', 0),
(732, 'pmb', 'docnum_in_database_allow', '1', 'Autoriser le stockage de document numérique en base ? \n 0 : Non.\n 1 : Oui.', '', 0),
(733, 'opac', 'recherche_ajax_mode', '1', 'Affichage accéléré des résultats de recherche: header uniquement, la suite est chargée lors du click sur le \"+\".\n 0: Inactif\n 1: Actif (par lot)\n 2: Actif (par notice)', 'c_recherche', 0),
(734, 'pmb', 'avis_note_display_mode', '1', 'Mode d\'affichage de la note pour les avis de notices.\n 0 : Note non visible.\n 1 : Affichage de la note sous la forme d\'étoiles.\n 2 : Affichage de la note sous la forme textuelle.\n 3 : Affichage de la note sous la forme textuelle et d\'étoiles.\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.\n 4 : Affichage de la note sous la forme d\'étoiles, choix de la note sous la forme d\'étoiles.\n 5 : Affichage de la note sous la forme textuelle et d\'étoiles, choix de la note sous la forme d\'étoiles.', '', 0),
(735, 'cms', 'active', '0', 'Module \'Portail\' activé.\n 0 : Non.\n 1 : Oui.', '', 0),
(736, 'pmb', 'indexation_lang', '', 'Choix de la langue d\'indexation par défaut. (ex : fr_FR,en_UK,...,ar), si vide c\'est la langue de l\'interface du catalogueur qui est utilisée.', '', 0),
(737, 'opac', 'websubscribe_show_location', '0', 'Afficher la possibilité pour le lecteur de choisir sa localisation lors de son inscription en ligne.\n 0: Non\n 1: Oui', 'f_modules', 0),
(738, 'opac', 'collstate_order', 'archempla_libelle,collstate_cote', 'Ordre d\'affichage des états des collections, dans l\'ordre donné, séparé par des virgules : archempla_libelle,collstate_cote', 'e_aff_notice', 0),
(739, 'opac', 'default_sort', 'd_num_6,c_text_1', 'Tri par défaut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))', 'd_aff_recherche', 0),
(740, 'pmb', 'fine_precision', '2', 'Nombre de décimales pour l\'affichage des amendes', '', 1),
(741, 'opac', 'search_cache_duration', '600', 'Durée de validité (en secondes) du cache des recherches OPAC', 'c_recherche', 0),
(742, 'pmb', 'path_php', '', 'Chemin absolu de l\'interpréteur PHP, local ou distant', '', 0),
(743, 'opac', 'explnum_order', 'explnum_mimetype, explnum_nom, explnum_id', 'Ordre d\'affichage des documents numériques, dans l\'ordre donné, séparé par des virgules : explnum_mimetype, explnum_nom, explnum_id', 'e_aff_notice', 0),
(744, 'pmb', 'amende_comptabilisation', '0', 'Date à laquelle le début de l\'amende sera comptabilisée \r\n 0 : à partir de la date de retour \r\n 1 : à partir du délai de grâce', '', 0),
(745, 'pmb', 'pret_calcul_retard_date_debut_incluse', '0', 'Compter le jour de retour ou de relance comme un jour de retard pour le calcul de l\'amende ? \r\n 0 : Non \r\n  1 : Oui', '', 0),
(746, 'opac', 'exclude_fields', '', 'Identifiants des champs à exclure de la recherche tous les champs (liste dispo dans le fichier includes/indexation/champ_base.xml)', 'c_recherche', 0),
(747, 'opac', 'serialcirc_active', '0', 'Activer la circulation des pédioques dans l\'OPAC \r\n 0: Non \r\n 1: Oui', 'f_modules', 0),
(748, 'pmb', 'bdd_subversion', '0', 'Sous-version de la base de données', '', 0),
(749, 'pmb', 'import_modele_authorities', 'authority_import', 'Quelle classe d\'import utiliser pour les notices d\'autorités ?', '', 0),
(750, 'pmb', 'location_resa_planning', '0', 'Utiliser la gestion de la prévision localisée?\n 0: Non\n 1: Oui', '', 0),
(751, 'demandes', 'email_demandes', '1', 'Information par email de l\'évolution des demandes.\n 0 : Non\n 1 : Oui', '', 0),
(752, 'pmb', 'short_loan_management', '0', 'Gestion des prêts courts\n 0: Non\n 1: Oui', '', 0),
(753, 'pmb', 'loan_trust_management', '0', 'Gestion de monopole de prêt\n 0: Non\n x: nombre de jours entre 2 prêts d\'un exemplaire d\'une même notice (ou bulletin)', '', 0),
(754, 'opac', 'perio_a2z_abc_search', '0', 'Recherche abécédaire dans le navigateur de périodiques en OPAC.\n0 : Non.\n1 : Oui.', 'c_recherche', 0),
(755, 'opac', 'perio_a2z_max_per_onglet', '10', 'Recherche dans le navigateur de périodiques en OPAC : nombre maximum de notices par onglet.', 'c_recherche', 0),
(756, 'pmb', 'indexation_docnum_ext', '', 'Paramètres de gestion d\'accès aux programmes externes pour l\'indexation des documents numériques :\n\n Chaque paramètre est défini par un  couple : \"nom=valeur\"\n Les paramètres sont séparés par un \"point-virgule\".\n\n\n Exemples d\'utilisation de \"pyodconverter\", \"jodconverter\" et \"pdftotext\" :\n\npyodconverter_cmd=/opt/openoffice.org3/program/python /opt/ooo_converter/DocumentConverter.py %1s %2s;\njodconverter_cmd=/usr/bin/java -jar /opt/ooo_converter/jodconverter-2.2.2/lib/jodconverter-cli-2.2.2.jar %1s %2s;\njodconverter_url=http://localhost:8080/converter/converted/%1s;\npdftotext_cmd=/usr/bin/pdftotext -enc UTF-8 %1s -;', '', 0),
(757, 'opac', 'notices_format_onglets', '', 'Liste des id de template de notice pour ajouter des onglets personnalisés en affichage de notice\nExemple: 1,3,ISBD,PUBLIC\nLe paramètre notices_format doit être à 0 pour placer ISBD et PUBLIC', 'e_aff_notice', 0),
(758, 'opac', 'visionneuse_alert', '', 'Message d\'alerte à l\'ouverture des documents numériques.', 'm_photo', 0),
(759, 'opac', 'cms', '0', 'id du CMS utilisé en OPAC', 'a_general', 0),
(760, 'pmb', 'expl_data', 'expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle', 'Colonnes des exemplaires, dans l\'ordre donné, séparé par des virgules : expl_cb,expl_cote,location_libelle,section_libelle,statut_libelle,tdoc_libelle,groupexpl_name,nb_prets #n : id des champs personnalisés \r\n expl_cb est obligatoire et sera ajouté si absent', '', 0),
(761, 'pmb', 'expl_display_location_without_expl', '0', 'Affichage de la liste des localisations sans exemplaire\n 0: Non\n 1: oui', '', 0),
(762, 'opac', 'show_group_checkout', '0', 'Le responsable du groupe de lecteur voit les prêts de son groupe\n 0: Non\n 1: oui', 'a_general', 0),
(763, 'opac', 'facette_in_bandeau_2', '0', 'La navigation par facettes apparait dans le bandeau ou dans le bandeau 2\n0 : dans le bandeau\n1 : Dans le bandeau 2', 'c_recherche', 0),
(764, 'opac', 'autolevel2', '1', '0 : mode normal de recherche.\n1 : Affiche le résultat de la recherche tous les champs après calcul du niveau 1 de recherche.\n2 : Affiche directement le résultat de la recherche tous les champs sans passer par le calcul du niveau 1 de recherche.', 'c_recherche', 0),
(765, 'opac', 'first_page_params', '', 'Structure Json récapitulant les paramètres à initialiser pour la page d\'accueil :\nExemple : \n{\n\"lvl\":\"cmspage\",\n\"pageid\":2\n}', 'b_aff_general', 0),
(766, 'opac', 'show_links_invisible_docnums', '0', 'Afficher les liens vers les documents numériques non visible en mode non connecté. (Ne fonctionne pas avec les droits d\'accès).\n 0 : Non.\n1 : Oui.', 'e_aff_notice', 0),
(767, 'pmb', 'img_folder', '', 'Répertoire de stockage des images', '', 0),
(768, 'pmb', 'img_url', '', 'URL d\'accès du répertoire des images (pmb_img_folder)', '', 0),
(769, 'pmb', 'book_pics_msg', '', 'Message sur le survol des vignettes des notices correspondant au chemin fourni par le paramètre book_pics_url', '', 0),
(770, 'opac', 'book_pics_msg', '', 'Message sur le survol des vignettes des notices correspondant au chemin fourni par le paramètre book_pics_url', 'e_aff_notice', 0),
(771, 'opac', 'visionneuse_alert_doctype', '', 'Liste des types de documents pour lesquels une alerte est générée (séparés par une virgule).', 'm_photo', 0),
(772, 'pmb', 'archive_warehouse', '0', 'Identifiant de l\'entrepôt d\'archivage à la suppression des notices.', '', 0),
(773, 'pmb', 'printer_name', '', 'Nom de l\'imprimante de ticket de prêt, utilisant l\'applet jzebra. Le nom de l\'imprimante doit correspondre à la class développée spécifiquement pour la piloter.\nExemple: Nommer l\'imprimante \'metapace\' pour utiliser le driver classes/printer/metapace.class.php\n\nSi l\'imprimante est connectée à un Raspberry Pi, indiquer l\'ip et le port\nExemple : raspberry@192.168.0.82:3000', '', 0),
(774, 'empr', 'groupes_localises', '0', 'Groupes de lecteurs localisés par rapport au responsable \n0: Non \n1: oui', '', 0),
(775, 'opac', 'allow_simili_search', '0', 'Activer les recherches similaires sur une notice :\n0 : Non\n1 : Activer la recherche \"Dans le même rayon\" et \"Peut-être aimerez-vous\"\n2 : Activer seulement la recherche \"Dans le même rayon\"\n3 : Activer seulement la recherche \"Peut-être aimerez-vous\"', 'e_aff_notice', 0),
(776, 'opac', 'notices_depliable_plus', 'plus.gif', 'Image à utiliser devant un titre de notice pliée', 'e_aff_notice', 0),
(777, 'opac', 'notices_depliable_moins', 'minus.gif', 'Image à utiliser devant un titre de notice dépliée', 'e_aff_notice', 0),
(778, 'pmb', 'pret_groupement', '0', 'Activer le prêt d\'exemplaires regroupés en un seul lot. La gestion des groupes se gére en Circulation / Groupe d\'exemplaires :\n 0 : non \n 1 : oui', '', 0),
(779, 'transferts', 'regroupement_depart', '0', 'Active le regroupement des départs\n 0: Non \n 1: Oui', '', 1),
(780, 'pmb', 'rfid_afi_security_codes', '', 'Gestion de l\'antivol par le registre AFI.\nLa première valeur est celle de l\'antivol actif, la deuxième est celle de lantivol inactif.\nExemple: 07,C2  ', '', 0),
(785, 'cms', 'url_base_cms_build', '', 'url de construction du CMS de l\'OPAC', '', 0),
(782, 'opac', 'simple_search_suggestions', '0', 'Activer la suggestion de mots en recherche simple via la complétion\n0 : Désactiver\n1 : Activer\n\nNB : Cette fonction nécessite l\'installation de l\'extension levenshtein dans MySQL', 'c_recherche', 0),
(783, 'opac', 'stemming_active', '0', 'Activer le stemming dans la recherche\n0 : Désactiver\n1 : Activer\n', 'c_recherche', 0),
(786, 'pdflettreretard', '1before_list_group', 'Sauf erreur de notre part, vous avez toujours en votre possession le ou les ouvrage(s) suivant(s) dont la durée de prêt est aujourd\'hui dépassée :', 'Texte apparaissant avant la liste des ouvrages en retard dans le courrier de relance de retard', '', 0),
(787, 'pdflettreretard', '1after_list_group', 'Nous vous remercions de prendre rapidement contact par téléphone au $biblio_phone ou par mail à $biblio_email pour étudier la possibilité de prolonger ces prêts ou de rapporter les ouvrages concernés.', 'Texte apparaissant après la liste des ouvrages en retard dans le courrier', '', 0),
(788, 'pdflettreretard', '1fdp_group', 'Le responsable.', 'Signataire de la lettre.', '', 0),
(789, 'pdflettreretard', '1madame_monsieur_group', 'Madame, Monsieur,', 'Entête de la lettre', '', 0),
(790, 'opac', 'show_bandeau_2', '1', 'Affichage du bandeau_2 ? \n 0 : Non\n 1 : Oui', 'f_modules', 0),
(791, 'opac', 'param_social_network', '{\n			\"token\":\"ra-4d9b1e202c30dea1\",\n			\"version\":\"300\",\n			\"buttons\":[\n			{\n			\"attributes\":{\n			\"class\":\"addthis_button_facebook_like\",\n			\"fb:like:layout\":\"button_count\"\n			}\n			},\n			{\n			\"attributes\":{\n			\"class\":\"addthis_button_tweet\"\n			}\n			},\n			{\n			\"attributes\":{\n			\"class\":\"addthis_counter addthis_button_compact\"\n			}\n			}\n			],\n			\"toolBoxParams\":{\n			\"class\":\"addthis_toolbox addthis_default_style\"\n			},\n			\"addthis_share\":{\n			\n			},\n			\"addthis_config\":{\n			\"data_track_clickback\":\"true\",\n			\"ui_click\":\"true\"\n			}\n			}\n			', 'Tableau de paramètrage de l\'API de gestion des interconnexions aux réseaux sociaux.\n			Au format JSON.\n			Exemple :\n			{\n			\"token\":\"ra-4d9b1e202c30dea1\",\n			\"version\":\"300\",\n			\"buttons\":[\n			{\n			\"attributes\":{\n			\"class\":\"addthis_button_preferred_1\"\n			}\n			},\n			{\n			\"attributes\":{\n			\"class\":\"addthis_button_preferred_2\"\n			}\n			},\n			{\n			\"attributes\":{\n			\"class\":\"addthis_button_preferred_3\"\n			}\n			},\n			{\n			\"attributes\":{\n			\"class\":\"addthis_button_preferred_4\"\n			}\n			},\n			{\n			\"attributes\":{\n			\"class\":\"addthis_button_compact\"\n			}\n			},\n			{\n			\"attributes\":{\n			\"class\":\"addthis_counter addthis_bubble_style\"\n			}\n			}\n			],\n			\"toolBoxParams\":{\n			\"class\":\"addthis_toolbox addthis_default_style addthis_32x32_style\"\n			},\n			\"addthis_share\":{\n			\n			},\n			\"addthis_config\":{\n			\"data_track_addressbar\":true\n			}\n			}', 'e_aff_notice', 0),
(816, 'opac', 'default_sort_list', '0 d_num_6,c_text_28||d_text_7', 'Afficher la liste déroulante de sélection d\'un tri ? \n 0 : Non \n 1 : Oui \nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)', 'd_aff_recherche', 0),
(792, 'transferts', 'pret_demande_statut', '0', 'Appliquer ce statut avant la validation', '', 1),
(793, 'opac', 'perio_a2z_show_bulletin_notice', '0', 'Affichage de la notice de bulletin dans le navigateur de périodiques', 'c_recherche', 0),
(794, 'pmb', 'procs_force_execution', '0', 'Permettre le forçage de l\'exécution des procédures', '', 0),
(795, 'opac', 'draggable', '1', 'Permet d\'activer le glisser déposer dans le panier pour l\'affichage des notices à l\'OPAC', 'e_aff_notice', 0),
(802, 'cms', 'cache_ttl', '1800', 'durée de vie du cache des cadres du portail (en secondes)', '', 0),
(796, 'opac', 'meta_description', '', 'Contenu du meta tag description pour les moteurs de recherche', 'b_aff_general', 0),
(797, 'opac', 'meta_keywords', '', 'Contenu du meta tag keywords pour les moteurs de recherche', 'b_aff_general', 0),
(798, 'opac', 'meta_author', '', 'Contenu du meta tag author pour les moteurs de recherche', 'b_aff_general', 0),
(799, 'pmb', 'html_allow_expl_cote', '0', 'Autoriser le code HTML dans les cotes exemplaires ? \n 0 : non \n 1', '', 0),
(800, 'pmb', 'default_style_addon', '', 'Ajout de styles CSS aux feuilles déjà incluses ?\n Ne mettre que le code CSS, exemple:  body {background-color: #FF0000;}', '', 0),
(801, 'pmb', 'serialcirc_subst', '', 'Nom du fichier permettant de personnaliser l\'impression de la liste de circulation des périodiques', '', 0),
(803, 'pmb', 'serial_thumbnail_url_article', '0', 'Préremplissage de l\'url de la vignette des dépouillements avec l\'url de la vignette de la notice mère en catalogage des périodiques ? \n 0 : Non \n 1 : Oui', '', 0),
(804, 'pmb', 'mail_delay', '0', 'Temps d\'attente en millisecondes entre chaque mail envoyé lors d\'un envoi groupé. \n 0 : Pas d\'attente', '', 0),
(805, 'pmb', 'curl_timeout', '5', 'Timeout cURL (en secondes) pour la vérification des liens', '', 1),
(806, 'empr', 'allow_prolong_members_group', '0', 'Autoriser la prolongation groupée des adhésions des membres d\'un groupe ? \n 0 : Non \n 1 : Oui', '', 0),
(807, 'thesaurus', 'auto_index_notice_fields', '', 'Liste des champs de notice à utiliser pour l\'indexation automatique.\n\nSyntaxe: nom_champ=poids_indexation;\n\nLes noms des champs sont ceux précisés dans le fichier XML \"pmb/includes/notice/notice.xml\"\nLe poids de l\'indexation est une valeur de 0.00 à 1. (Si rien n\'est précisé, le poids est de 1)\n\nExemple :\n\ntit1=1.00;n_resume=0.5;', 'categories', 0),
(808, 'thesaurus', 'auto_index_search_param', '', 'Surchage des paramètres de recherche de l\'indexation automatique.\nSyntaxe: param=valeur;\n\nListes des parametres:\n\nmax_relevant_words = 20 (nombre maximum de mots et de lemmes de la notice à prendre en compte pour le calcul)\n\nautoindex_deep_ratio = 0.05 (ratio sur la profondeur du terme dans le thésaurus)\nautoindex_stem_ratio = 0.80 (ratio de pondération des lemmes / aux mots)\n\nautoindex_max_up_distance = 2 (distance maximum de recherche dans les termes génériques du thésaurus)\nautoindex_max_up_ratio = 0.01 (pondération sur les termes génériques)\n\nautoindex_max_down_distance = 2 (distance maximum de recherche dans les termes spécifiques du thésaurus)\nautoindex_max_down_ratio = 0.01 (pondération sur les termes spécifiques)\n\nautoindex_see_also_ratio = 0.01 (surpondération sur les termes voir aussi du thésaurus)\n\nautoindex_distance_type = 1 (calcul de distance de 1 à 4)\nautoindex_distance_ratio = 0.50 (ratio de pondération sur la distance entre les mots trouvés et les termes d\'une expression du thésaurus)\n\nmax_relevant_terms = 10 (nombre maximum de termes retournés)', 'categories', 0),
(809, 'empr', 'abonnement_default_debit', '0', 'Choix par défaut pour la prolongation des lecteurs. \n 0 : Ne pas débiter l\'abonnement \n 1 : Débiter l\'abonnement sans la caution \n 2 : Débiter l\'abonnement et la caution', '', 0),
(833, 'demandes', 'notice_auto', '0', 'Création automatique de la notice de demande :\n0 : Non\n1 : Oui', '', 0),
(834, 'demandes', 'default_action', '1', 'Création par défaut d\'une action lors de la validation de la demande :\n0 : Non\n1 : Oui', '', 0),
(835, 'pmb', 'synchro_rdf', '0', 'Activer la synchronisation rdf\n 0 : non \n 1 : oui (l\'activation de ce paramètre nécessite une ré-indexation)', '', 0),
(811, 'opac', 'print_template_default', '0', 'En impression de panier, identifiant du template de notice utilisé par défaut. Si vide ou à 0, le template classique est utilisé', 'a_general', 0),
(812, 'pmb', 'show_permalink', '0', 'Afficher le lien permanent de l\'OPAC en gestion ? \n 0 : Non.\n 1 : Oui.', '', 0),
(813, 'pmb', 'expl_show_lastempr', '1', 'Afficher l\'emprunteur précédent sur la fiche exemplaire ? \n 0 : Non.\n 1 : Oui.', '', 0),
(814, 'pmb', 'gestion_financiere_caisses', '0', 'Activer la gestion de caisses en gestion financière? \n 0 : Non.\n 1 : Oui.', '', 0),
(815, 'pmb', 'diarization_docnum', '0', 'Activer la segmentation des documents numériques vidéo ou audio 0 : non activée 1 : activée', '', 0),
(817, 'opac', 'default_sort_display', '0', 'Afficher le libellé du tri appliqué par défaut en résultat de recherche ? \n 0 : Non \n 1 : Oui', 'd_aff_recherche', 0),
(818, 'opac', 'show_bannettes', '0', 'Affichage des bannettes en page d\'accueil OPAC.\n 0 : Non.\n 1 : Oui.', 'f_modules', 0),
(819, 'opac', 'facettes_ajax', '1', 'Charger les facettes en ajax\n0 : non\n1 : oui', 'c_recherche', 0),
(820, 'opac', 'search_all_keep_empty_words', '1', 'Conserver les mots vides pour les autorités dans la recherche tous les champs\n0 : non\n1 : oui', 'c_recherche', 0),
(821, 'pmb', 'pret_already_loaned', '0', 'Activer le piège en prêt si le document a déjà été emprunté par le lecteur. Nécessite l\'activation de l\'archivage des prêts\n0 : non\n1 : oui', '', 0),
(823, 'opac', 'nb_notices_similaires', '6', 'Nombre de notices similaires affichées lors du dépliage d\'une notice.\nValeur max = 6.', 'e_aff_notice', 0),
(824, 'opac', 'notice_reduit_format_similaire', '1', 'Format d\'affichage des réduits de notices similaires :\n 0 = titre+auteur principal\n 1 = titre+auteur principal+date édition\n 2 = titre+auteur principal+date édition + ISBN\n 3 = titre seul\n P 1,2,3 = tit+aut+champs persos id 1 2 3\n E 1,2,3 = tit+aut+édit+champs persos id 1 2 3\n T = tit1+tit4\n 4 = titre+titre parallèle+auteur principal\n H 1 = id d\'un template de notice', 'e_aff_notice', 0),
(825, 'opac', 'search_noise_limit_type', '0', 'Ecrêter les résulats de recherche en fonction de la pertinence. \n0 : Non \n1 : Retirer du résultat tout ce qui est en dessous de la moyenne - l\'écart-type\n2,ratio : Retirer du résultat tout ce qui est en dessous de la moyenne - un ratio de l\'écart-type (ex: 2,1.96)\n3,ratio : Retirer du résultat tout ce qui est dessous d\'un ratio de la pertinence max (ex: 3,0.25 élimine tout ce qui est inférieur à 25% de la plus forte pertinence)', 'c_recherche', 0),
(826, 'opac', 'search_relevant_with_frequency', '0', 'Utiliser la fréquence d\'apparition des mots dans les notices pour le calcul de la pertinence.\n0 : Non \n1 : Oui', 'c_recherche', 0),
(827, 'empr', 'prolong_calc_date_adhes_depassee', '0', 'Si la date d\'adhésion est dépassée, le calcul de la prolongation se fait à partir de :\n 0 : la date de fin d\'adhésion\n 1 : la date du jour', '', 0),
(828, 'opac', 'bannette_priv_periodicite', '15', 'Périodicité d\'envoi par défaut en création de bannette privée (en jours)', 'l_dsi', 0),
(829, 'opac', 'show_subscribed_bannettes', '0', 'Affichage des bannettes auxquelles le lecteur est abonné en page d\'accueil OPAC :\n0 : Non.\n1 : Oui.', 'f_modules', 0),
(830, 'opac', 'show_public_bannettes', '0', 'Affichage des bannettes sélectionnées en page d\'accueil OPAC :\n0 : Non.\n1 : Oui.', 'f_modules', 0),
(836, 'faq', 'active', '0', 'Module \'FAQ\' activé.\n 0 : Non.\n 1 : Oui.', '', 0),
(837, 'opac', 'websubscribe_num_carte_auto', '1', 'Numéro de carte de lecteur automatique ?\n 1: www + Identifiant du lecteur \n 2,a,b,c: a=longueur du préfixe, b=nombre de chiffres de la partie numérique, c=préfixe fixé (facultatif)\n 3,fonction: fonction de génération spécifique dans fichier nommé de la même façon, à placer dans pmb/opac_css/circ/empr', 'f_modules', 0),
(838, 'pdfcartelecteur', 'printer_card_handler', '', 'Gestionnaire d\'impression :\n\n 1 = script \"print_cb.php\"\n 2 = applet jzebra\n 3 = requête ajax', '', 0),
(839, 'pdfcartelecteur', 'printer_card_name', '', 'Nom de l\'imprimante.', '', 0),
(840, 'pdfcartelecteur', 'printer_card_url', '', 'Adresse de l\'imprimante.', '', 0),
(841, 'pmb', 'notice_img_folder_id', '0', 'Identifiant du répertoire d\'upload des vignettes de notices', '', 0),
(842, 'pmb', 'compare_notice_template', '0', 'Choix du template d\'affichage des notices en mode comparaison.', '', 1),
(843, 'pmb', 'compare_notice_nb', '5', 'Nombre de notices à afficher et à raffraichir en mode comparaison.', '', 1),
(844, 'opac', 'compare_notice_active', '1', 'Activer le comparateur de notices', 'c_recherche', 0),
(845, 'pmb', 'autorites_verif_js', '', 'Script de vérification de saisie des autorités', '', 0),
(846, 'opac', 'resa_cart', '1', 'Paramètre pour masquer/afficher la reservation par panier\n0 : Non \n1 : Oui', 'a_general', 0),
(847, 'pmb', 'search_stemming_active', '0', 'Activer le stemming dans la recherche\n0 : Désactiver\n1 : Activer', 'search', 0),
(848, 'pmb', 'search_exclude_fields', '', 'Identifiants des champs à exclure de la recherche tous les champs (liste dispo dans le fichier includes/indexation/champ_base.xml)', 'search', 0),
(849, 'pmb', 'search_noise_limit_type', '0', 'Ecrêter les résulats de recherche en fonction de la pertinence. \n0 : Non \n1 : Retirer du résultat tout ce qui est en dessous de la moyenne - l\'écart-type\n2,ratio : Retirer du résultat tout ce qui est en dessous de la moyenne - un ratio de l\'écart-type (ex: 2,1.96)\n3,ratio : Retirer du résultat tout ce qui est dessous d\'un ratio de la pertinence max (ex: 3,0.25 élimine tout ce qui est inférieur à 25% de la plus forte pertinence)', 'search', 0),
(850, 'pmb', 'search_relevant_with_frequency', '0', 'Utiliser la fréquence d\'apparition des mots dans les notices pour le calcul de la pertinence.\n0 : Non \n1 : Oui', 'search', 0),
(851, 'pmb', 'allow_term_troncat_search', '0', 'Troncature à droite automatique :\n0 : Non \n1 : Oui', 'search', 0),
(852, 'pmb', 'search_cache_duration', '0', 'Durée de validité (en secondes) du cache des recherches', 'search', 0),
(853, 'pmb', 'print_expl_default', '0', 'En impression de panier, imprimer les exemplaires est coché par défaut \n 0 : Non \n 1 : Oui', '', 0),
(854, 'thesaurus', 'concepts_active', '0', 'Active ou non l\'utilisation des concepts:\n0 : Non\n1 : Oui', 'concepts', 0),
(855, 'thesaurus', 'concepts_affichage_ordre', '0', 'Paramétrage de l\'ordre d\'affichage des concepts d\'une notice.\nPar ordre alphabétique: 0(par défaut)\nPar ordre de saisie: 1', 'concepts', 0),
(856, 'thesaurus', 'concepts_concept_in_line', '0', 'Affichage des concepts en ligne.\n 0 : Non.\n 1 : Oui.', 'concepts', 0),
(857, 'opac', 'collstate_data', '', 'Colonne des états des collections, dans l\'ordre donné, séparé par des virgules : location_libelle,emplacement_libelle,cote,type_libelle,statut_opac_libelle,origine,state_collections,archive,lacune,surloc_libelle,note\nLes valeurs possibles sont les propriétés de la classe PHP \"pmb/opac_css/classes/collstate.class.php\".', 'e_aff_notice', 0),
(858, 'thesaurus', 'ontology_filemtime', 'a:1:{s:8:\"ontology\";i:1675604421;}', 'Paramètre caché pour conservation de la date de dernière modification de l\'ontologie', 'ontologie', 1),
(859, 'gestion_acces', 'empr_docnum', '0', 'Gestion des droits d\'accès des emprunteurs aux documents numériques\n0 : Non.\n1 : Oui.', '', 0),
(860, 'gestion_acces', 'empr_docnum_def', '0', 'Valeur par défaut en modification de document numérique pour les droits d\'accès emprunteurs - documents numériques\n0 : Recalculer.\n1 : Choisir.', '', 0),
(861, 'transferts', 'retour_action_resa', '1', 'Génére un transfert pour répondre à une réservation lors du retour de l\'exemplaire\n 0: Non\n 1: Oui', '', 1),
(862, 'pmb', 'logs_exclude_robots', '1', 'Exclure les robots dans les logs OPAC ?\n 0: Non\n 1: Oui. \nFaire suivre d\'une virgule pour éventuellement exclure les logs OPAC provenant de certaines adresses IP, elles-mêmes séparées par des virgules (ex : 1,127.0.0.1,192.168.0.1).', '', 0),
(863, 'pmb', 'map_activate', '0', 'Activation du géoréférencement', 'map', 0),
(864, 'pmb', 'map_max_holds', '250,0', 'Dans l\'ordre donné séparé par une virgule: Nombre limite d\'emprises affichées, mode de clustering \nValeurs possibles pour le mode :\n\n0 => Clustering standard avec augmentation dynamique des seuils jusqu\'a atteindre le nombre maximum d\'emprises affichées\n\n1 => Clusterisation de toutes les emprises', 'map', 0),
(878, 'opac', 'map_holds_record_color', '#D6A40F', 'Couleur des emprises associées à des notices', 'map', 0),
(865, 'pmb', 'map_holds_record_color', '#D6A40F', 'Couleur des emprises associées à des notices', 'map', 0),
(866, 'pmb', 'map_holds_authority_color', '#D60F0F', 'Couleur des emprises associées à des autorités', 'map', 0),
(867, 'pmb', 'map_base_layer_type', 'OSM', 'Fonds de carte à utiliser.\nValeurs possibles :\nOSM           => Open Street Map\nWMS           => The Web Map Server base layer type selector.\nGOOGLE        => Google\nARCGIS        =>The ESRI ARCGis base layer selector.\n', 'map', 0),
(868, 'pmb', 'map_base_layer_params', '', 'Structure JSON à passer au fond de carte\nexemple :\n{\n \"name\": \"Nom du fond de carte\",\n \"url\": \"url du fond de carte\",\n \"options\":{\n  \"layers\": \"MONDE_MOD1\"\n }\n}', 'map', 0),
(869, 'pmb', 'map_size_search_edition', '800*480', 'Taille de la carte en saisie de recherche. En pixels ou en pourcentage. Exemple : 100%*480px', 'map', 0),
(870, 'pmb', 'map_size_search_result', '800*480', 'Taille de la carte en résultat de recherche. En pixels ou en pourcentage. Exemple : 100%*480px', 'map', 0),
(871, 'pmb', 'map_size_notice_view', '800*480', 'Taille de la carte en visualisation de notice. En pixels ou en pourcentage. Exemple : 100%*480px', 'map', 0),
(872, 'pmb', 'map_size_notice_edition', '800*480', 'Taille de la carte en édition de notice. En pixels ou en pourcentage. Exemple : 100%*480px', 'map', 0),
(873, 'pmb', 'notice_img_pics_max_size', '150', 'Taille maximale des vignettes uploadées dans les notices, en largeur ou en hauteur', '', 0),
(874, 'opac', 'map_activate', '0', 'Activation de la géolocalisation:\n 0 : Non \n 1 : Pour toutes les cartes \n 2 : Seulement pour les cartes de notices \n 3 : Seulement pour les cartes de localisation des exemplaires', 'map', 0),
(875, 'pmb', 'psexec_cmd', 'psexec -d', 'Paramètres de lancement de psexec (planificateur sous windows)\r\n\nAjouter l\'option -accepteula sur les versions les plus récentes. ', '', 0),
(876, 'pmb', 'editorial_dojo_editor', '1', 'Activation de l\'éditeur DoJo dans le contenu éditorial:\n 0 : non \n 1 : oui', '', 0),
(877, 'opac', 'map_max_holds', '250,0', 'Dans l\'ordre donné séparé par une virgule: Nombre limite d\'emprises affichées, mode de clustering \nValeurs possibles pour le mode :\n\n0 => Clustering standard avec augmentation dynamique des seuils jusqu\'a atteindre le nombre maximum d\'emprises affichées\n\n1 => Clusterisation de toutes les emprises', 'map', 0),
(879, 'opac', 'map_holds_authority_color', '#D60F0F', 'Couleur des emprises associées à des autorités', 'map', 0),
(880, 'opac', 'map_size_search_edition', '800*480', 'Taille de la carte en saisie de recherche. En pixels ou en pourcentage. Exemple : 100%*480px', 'map', 0),
(881, 'opac', 'map_size_search_result', '800*480', 'Taille de la carte en résultat de recherche. En pixels ou en pourcentage. Exemple : 100%*480px', 'map', 0),
(882, 'opac', 'map_size_notice_view', '800*480', 'Taille de la carte en visualisation de notice. En pixels ou en pourcentage. Exemple : 100%*480px', 'map', 0),
(883, 'opac', 'map_base_layer_type', 'OSM', 'Fonds de carte à utiliser.\nValeurs possibles :\nOSM           => Open Street Map\nWMS           => The Web Map Server base layer type selector.\nGOOGLE        => Google\nARCGIS        =>The ESRI ARCGis base layer selector.\n', 'map', 0),
(884, 'opac', 'map_base_layer_params', '', 'Structure JSON à passer au fond de carte\nexemple :\n{\n \"name\": \"Nom du fond de carte\",\n \"url\": \"url du fond de carte\",\n \"options\":{\n  \"layers\": \"MONDE_MOD1\"\n }\n}', 'map', 0),
(885, 'acquisition', 'budget_show_all', '0', 'Sélection d\'une rubrique budgétaire en commande : toutes les afficher ?\n 0: Non (par pagination)\n 1: Oui.', '', 0),
(886, 'pmb', 'abt_label_perio', '0', 'Création d\'un abonnement : reprendre le nom du périodique ?\n 0: Non \n 1: Oui.', '', 0),
(887, 'acquisition', 'show_abt_in_cmde', '0', 'Afficher l\'abonnement dans les lignes de la commande ?\n 0: Non \n 1: Oui.', '', 0),
(888, 'pmb', 'nomenclature_record_children_link', '', 'Type de relation entre une notice de nomenclature et ses notices filles.', '', 1),
(889, 'pmb', 'nomenclature_activate', '0', 'Activation des nomenclatures:\n 0 : non \n 1 : oui', '', 0),
(890, 'demandes', 'email_generic', '', 'Information par un email générique de l\'évolution des demandes.\n 1,adrmail@mail.fr : Envoi une copie uniquement pour toutes les nouvelles demandes\n 2,adrmail@mail.fr : Envoi une copie uniquement des mails envoyés aux personnes affectées\n 3,adrmail@mail.fr : Envoi une copie dans les 2 cas précédents\n ', '', 0),
(891, 'opac', 'demandes_affichage_simplifie', '0', 'Active le format simplifié des demandes en Opac:\n 0 : non \n 1 : oui', 'a_general', 0),
(892, 'opac', 'demandes_no_action', '0', 'Interdire l\'ajout d\'une action en Opac:\n 0 : non \n 1 : oui', 'a_general', 0),
(893, 'pmb', 'map_hold_ratio_min', '4', 'Ratio minimum d\'occupation en pourcentage d\'une emprise pour s\'afficher', 'map', 0),
(894, 'pmb', 'map_hold_ratio_max', '75', 'Ratio maximum d\'occupation en pourcentage d\'une emprise pour s\'afficher', 'map', 0),
(895, 'pmb', 'map_hold_distance', '10', 'Rapport de distance entre deux points pour les agréger', 'map', 0),
(896, 'opac', 'modules_search_concept', '0', 'Recherche dans les concepts : \n 0 : interdite, \n 1 : autorisée, \n 2 : autorisée et validée par défaut, \n -1 : également interdite en recherche multi-critères', 'c_recherche', 0),
(897, 'opac', 'map_hold_ratio_min', '4', 'Ratio minimum d\'occupation en pourcentage d\'une emprise pour s\'afficher', 'map', 0),
(898, 'opac', 'map_hold_ratio_max', '75', 'Ratio maximum d\'occupation en pourcentage d\'une emprise pour s\'afficher', 'map', 0),
(899, 'opac', 'map_hold_distance', '10', 'Rapport de distance entre deux points pour les agréger', 'map', 0),
(900, 'ldap', 'encoding_utf8', '0', 'Les informations du LDAP sont en utf-8 ?\n 0: Non \n 1: Oui.', '', 0),
(926, 'opac', 'script_analytics', '', 'Code Javascript d\'analyse d\'audience (Par exemple pour Google Analytics, XiTi,..).', 'a_general', 0),
(927, 'opac', 'accessibility', '1', 'Accessibilité activée.\n 0 : Non.\n 1 : Oui.', 'a_general', 0),
(928, 'pmb', 'newrecord_timeshift', '0', 'Nombre de jours de conservation des notices en tant que nouveauté.', '', 0),
(929, 'opac', 'notices_format_django_directory', '', 'Nom du répertoire de templates django à utiliser en affichage de notice.\nLaisser vide pour utiliser le common.', 'e_aff_notice', 0),
(930, 'opac', 'allow_download_docnums', '1', 'Autoriser le téléchargement des documents numériques.\n 0 : Non.\n 1 : Individuellement (un par un).\n 2 : Archive ZIP.', 'a_general', 0),
(931, 'opac', 'notices_display_modes', '', 'Nom du fichier xml de paramétrage du choix du mode d\'affichage des notices à l\'OPAC.\nPar défaut : display_modes_exemple.xml dans /opac_css/includes/records/', 'd_aff_recherche', 0),
(932, 'opac', 'url_more_about_cookies', '', 'Lien pour en savoir plus sur l\'utilisation des cookies et des traceurs', 'a_general', 0),
(933, 'opac', 'authorities_templates_folder', 'common', 'Repertoire des templates utilisés pour l\'affichage des autorités en OPAC', '', 1),
(934, 'dsi', 'private_bannette_notices_template', '0', 'Id du template de notice utilisé par défaut en diffusion de bannettes privées. Si vide ou à 0, le template classique est utilisé.', '', 0),
(935, 'cms', 'active_image_cache', '0', 'Activer la mise en cache des vignettes du contenu éditorial.\n 0: non \n 1:Oui \nAttention, si l\'OPAC ne se trouve pas sur le même serveur que la gestion, la purge du cache ne peut pas se faire automatiquement', '', 0),
(936, 'opac', 'empr_password_salt', '', 'Phrase pour le hashage des mots de passe emprunteurs', 'a_general', 1),
(937, 'pmb', 'notices_show_dates', '0', 'Afficher les dates des notices ? \n 0 : Aucune date.\n 1 : Date de création et modification.', '', 0),
(938, 'opac', 'compress_css', '0', 'Activer la compilation et la compression des feuilles de styles.\n0: Non\n1: Oui', 'a_general', 0),
(939, 'pmb', 'resa_planning_toresa', '10', 'Délai d\'alerte pour le transfert des prévisions en réservations (en jours). ', '', 0),
(940, 'pmb', 'serialcirc_simple_print_script', '', 'Script de construction d\'étiquette de circulation simplifiée de périodique', '', 0);
INSERT INTO `parametres` VALUES
(941, 'opac', 'max_results_on_a_page', '500', 'Nombre maximum de notices à afficher sur une page, utile notamment quand la navigation est désactivée', 'd_aff_recherche', 0),
(942, 'pmb', 'mail_adresse_from', '', 'Adresse d\'expédition des emails. Ce paramètre permet de forcer le From des mails envoyés par PMB. Le reply-to reste inchangé (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou paramètre opac_biblio_mail à défaut).\nFormat : adresse_email;libellé\nExemple : pmb@sigb.net;PMB Services', '', 0),
(943, 'opac', 'mail_adresse_from', '', 'Adresse d\'expédition des emails. Ce paramètre permet de forcer le From des mails envoyés par PMB. Le reply-to reste inchangé (mail de l\'utilisateur en DSI ou relance, mail de la localisation ou paramètre opac_biblio_mail à défaut).\nFormat : adresse_email;libellé\nExemple : pmb@sigb.net;PMB Services', 'a_general', 0),
(944, 'opac', 'pret_prolongation_blocage', '0', 'Bloquer la prolongation s\'il y a un niveau de relance validé sur le prêt ?\n0 : Non 1 : Oui', 'a_general', 0),
(945, 'opac', 'empr_export_loans', '0', 'Afficher sur le compte emprunteur un bouton permettant d\'exporter les prêts dans un tableur ?\n0 : Non 1 : Oui', 'a_general', 0),
(946, 'opac', 'cookies_consent', '1', 'Afficher le bandeau d\'acceptation des cookies et des traceurs ? \n0 : Non 1 : Oui', 'a_general', 0),
(947, 'pmb', 'collstate_data', '', 'Colonne des états des collections, dans l\'ordre donné, séparé par des virgules : location_libelle,emplacement_libelle,cote,type_libelle,statut_opac_libelle,origine,state_collections,archive,lacune,surloc_libelle,note,#n : id des champs personnalisés\nLes valeurs possibles sont les propriétés de la classe PHP \"pmb/classes/collstate.class.php\".', '', 0),
(948, 'opac', 'quick_access', '1', 'Activer le sélecteur d\'accès rapide ? \n0 : Non 1 : Oui', 'a_general', 0),
(949, 'pmb', 'resa_alert_localized', '0', 'Si les lecteurs sont localisés, restreindre les notifications par email des nouvelles réservations aux utilisateurs selon le site de gestion des lecteurs par défaut ? \n0 : Non 1 : Oui', '', 0),
(950, 'pmb', 'catalog_verif_js_integration', '', 'Script de vérification de saisie de notice en intégration', '', 0),
(951, 'opac', 'demandes_allow_from_record', '0', 'Autoriser les lecteurs à créer une demande à partir d\'une notice.\n 0 : Non\n 1 : Oui', 'a_general', 0),
(952, 'transferts', 'ghost_expl_enable', '0', 'Script de generation utilise pour les codes barres d\'exemplaires fantomes', '', 1),
(953, 'transferts', 'ghost_statut_expl_transferts', '0', 'id du statut dans lequel seront placés les exemplaires fantomes en cours de transit', '', 1),
(954, 'transferts', 'ghost_expl_gen_script', 'gen_code/gen_code_exemplaire.php', 'Script de generation utilise pour les codes barres d\'exemplaires fantomes', '', 1),
(955, 'pmb', 'extended_search_dnd_interface', '1', 'Activer l\'interface drag\'n\'drop pour la recherche multicritère.\n0 : Non\n1 : Oui', '', 0),
(956, 'opac', 'extended_search_dnd_interface', '0', 'Activer l\'interface drag\'n\'drop pour la recherche multicritère.\n0 : Non\n1 : Oui', 'c_recherche', 0),
(957, 'pmb', 'form_authorities_editables', '1', 'Grilles d\'autorités éditables \n 0 non \n 1 oui', '', 0),
(958, 'pmb', 'nb_elems_per_tab', '20', 'Nombre d\'éléments affichés par page dans les onglets', '', 0),
(959, 'pmb', 'authors_qualification', '0', 'Activer qualification d\'un lien d\'auteur dans les notices et les titres uniformes\n 0 : Non\n 1 : Oui', '', 0),
(960, 'opac', 'navigateur_bulletin_number', '3', 'Nombre de bulletins à afficher dans le navigateur de bulletins', 'e_aff_notice', 0),
(961, 'pmb', 'authority_mapping_folder', '', 'Dossier des classes de mappage à utiliser pour les autorités', '', 0),
(962, 'pmb', 'scan_request_activate', '0', 'Activer la demande de numérisation.\n0 : Non\n1 : Oui', '', 0),
(963, 'opac', 'scan_request_activate', '0', 'Activer la demande de numérisation.\n0 : Non\n1 : Oui', 'f_modules', 0),
(964, 'opac', 'scan_request_create_status', '1', 'Statut de création à l\'OPAC', 'a_general', 1),
(965, 'opac', 'scan_request_cancel_status', '1', 'Statut après annulation à l\'OPAC', 'a_general', 1),
(966, 'pmb', 'scan_request_explnum_folder', '0', 'Répertoire d\'upload des documents numériques liés aux demandes de numérisation', '', 1),
(967, 'opac', 'shared_lists_add_empr', '0', 'Afficher la possibilité pour le lecteur d\'inscrire d\'autres membres à ses listes de lecture partagées \n 0 : Non \n 1 : Oui', 'a_general', 0),
(968, 'pmb', 'nomenclature_music_concept_before', '0', 'URI du concept à associer aux partitions avant exécution', '', 1),
(969, 'pmb', 'nomenclature_music_concept_after', '0', 'URI du concept à associer aux partitions après exécution', '', 1),
(970, 'pmb', 'nomenclature_music_concept_blank', '0', 'URI du concept à associer aux partitions originales', '', 1),
(971, 'pmb', 'dashboard_quick_params_activate', '1', 'Activer les actions rapides dans le tableau de bord.\n0 : Non\n1 : Oui', '', 0),
(972, 'opac', 'avis_default_private', '0', 'Avis privé par défaut ? \n 0 : Non \n 1 : Oui', 'a_general', 0),
(973, 'pmb', 'bulletin_thumbnail_url_article', '0', 'Préremplissage de l\'url de la vignette des dépouillements avec l\'url de la vignette de la notice bulletin en catalogage des périodiques ? \n 0 : Non \n 1 : Oui', '', 0),
(974, 'pmb', 'form_expl_editables', '1', 'Grilles exemplaires éditables ? \n 0 non \n 1 oui', '', 0),
(975, 'pmb', 'form_explnum_editables', '1', 'Grilles exemplaires numériques éditables ? \n 0 non \n 1 oui', '', 0),
(976, 'pmb', 'contact_form_parameters', '', 'Paramétrage général du formulaire de contact', '', 1),
(977, 'pmb', 'contact_form_recipients_lists', '', 'Paramétrage des listes de destinataires du formulaire de contact', '', 1),
(978, 'opac', 'contact_form', '0', 'Afficher le formulaire de contact ? \n0 : Non 1 : Oui', 'a_general', 0),
(979, 'opac', 'short_url', '1', 'Afficher le lien permettant de générer un flux RSS de la recherche ? \n0 : Non 1 : Oui', 'd_aff_recherche', 0),
(980, 'pmb', 'collstate_advanced', '0', 'Activer la gestion avancée des états des collections :\n 0 : non activée \n 1 : activée', '', 0),
(981, 'opac', 'items_pagination_custom', '25,50,100,200', 'Personnalisation de valeurs numériques supplémentaires dans les listes paginées, séparées par des virgules : 25,50,100,200.', 'a_general', 0),
(982, 'pmb', 'items_pagination_custom', '25,50,100,200', 'Personnalisation de valeurs numériques supplémentaires dans les listes paginées, séparées par des virgules : 25,50,100,200.', '', 0),
(983, 'pmb', 'login_message', '', 'Message à afficher sur la page de connexion', '', 0),
(984, 'pmb', 'allow_search_into_linked_elements', '2', 'Afficher la case à cocher permettant d\'étendre la recherche aux oeuvres ?\n 0 : Non\n 1 : Oui, cochée par défaut\n 2 : Oui, non-cochée par défaut', 'search', 0),
(985, 'opac', 'allow_search_into_linked_elements', '2', 'Afficher la case à cocher permettant d\'étendre la recherche aux oeuvres ?\n 0 : Non\n 1 : Oui, cochée par défaut\n 2 : Oui, non-cochée par défaut', 'c_recherche', 0),
(986, 'opac', 'short_url_mode', '0', 'Elements générés dans le flux rss de la recherche: \n0 : Nouveautés \n1 : Résultats de la recherche \nPour le mode 1, un nombre de résultats limite peut être ajouté après le mode, il doit être précédé d\'une virgule\nExemple: 1,30\nSi aucune limite n\'est spécifiée, c\'est le paramètre opac_search_results_per_page qui sera pris en compte', 'd_aff_recherche', 0),
(987, 'pmb', 'resa_records_no_expl', '0', 'Réservation sur les notices \n 0 : Non \n 1 : Oui', '', 0),
(988, 'opac', 'quick_access_logout', '0', 'Activer le menu de déconnexion dans le sélecteur d\'accès rapide ? \n0 : Non 1 : Oui', 'a_general', 0),
(989, 'empr', 'pics_folder', '', 'Répertoire et motif d\'upload des photos des emprunteurs, dans le motif fourni, !!num_carte!! sera remplacé par le numéro de carte du lecteur. \n exemple : ./photos/lecteurs/!!num_carte!!.jpg \n ATTENTION : cohérence avec empr_pics_url à vérifier', '', 0),
(990, 'opac', 'suggestion_search_notice_doublon', '0', 'Activer la recherche de notices déjà présentes en saisie d\'une suggestion d\'acquisition\n0 : Désactiver\n1 : Activer\n2 : Activer avec le levenshtein\n    NB : Cette fonction nécessite l\'installation de l\'extension levenshtein dans MySQL', 'c_recherche', 0),
(991, 'opac', 'search_allow_refinement', '1', 'Afficher le lien \"Affiner la recherche\" en résultat de recherche', 'c_recherche', 0),
(992, 'pmb', 'map_holds_location_color', '#D60F0F', 'Couleur des emprises associées à des localisations', 'map', 0),
(993, 'pmb', 'map_size_location_edition', '800*480', 'Taille de la carte en édition de localisation en pixels, L*H, exemple : 800*480', 'map', 0),
(994, 'pmb', 'map_size_location_view', '800*480', 'Taille de la carte en visualisation des localisations en pixels, L*H, exemple : 800*480', 'map', 0),
(995, 'opac', 'map_holds_location_color', '#D60F0F', 'Couleur des emprises associées à des localisations en RVB, exemple : #D60F0F', 'map', 0),
(996, 'opac', 'map_size_location_view', '800*480', 'Taille de la carte en visualisation des localisationsen pixels, L*H, exemple : 800*480', 'map', 0),
(997, 'dsi', 'private_bannette_tpl', '0', 'Identifiant du template de bannette à appliquer sur les bannettes privées \nSi vide ou à 0, l\'entête et pied de page par défaut seront utilisés.', '', 0),
(998, 'pdflettreresa', 'resa_prolong_email', '0', 'Envoi d\'un mail au réservataire de rang 1, dont réservation validée, lorsque le prêt en cours est prolongé \n Non : 0 \n Oui : id_template ', '', 0),
(999, 'opac', 'recherche_show_expand', '1', 'Affichage des boutons de dépliage de toutes les notices dans les listes de résultats à l\'OPAC \n0: Boutons non affichés \n1: Boutons affichés', 'c_recherche', 0),
(1000, 'cms', 'active_toolkits', '0', 'Activer la possibilité de gérer des toolkits.\n 0: non \n 1:Oui', '', 0),
(1001, 'pmb', 'compare_notice_active', '1', 'Activer le comparateur de notices', '', 0),
(1002, 'acquisition', 'request_type_pref_account', '', 'Mémorise le décompte préféré à un type de demande', '', 1),
(1003, 'pmb', 'prefill_prix', '0', 'Préremplissage du prix des exemplaires avec le prix indiqué en notice ? \n 0 : Non \n 1 : Oui', '', 0),
(1004, 'opac', 'map_holds_sur_location_color', '#D60F0F', 'Couleur des emprises associées à des sur-localisations', 'map', 0),
(1005, 'opac', 'map_size_location_facette', '100%*200px', 'Taille de la carte des localisations dans les facettes. En pixels ou en pourcentage. Exemple : 100%*200px', 'map', 0),
(1006, 'opac', 'map_size_location_home_page', '100%*200px', 'Taille de la carte des localisations dans la page d\'accueil de l\'Opac. En pixels ou en pourcentage. Exemple : 100%*480px', 'map', 0),
(1007, 'pmb', 'scan_request_location_activate', '0', 'Activer la localisation d\'une demande de numérisation', '', 0),
(1008, 'opac', 'print_explnum', '1', 'Activer la possibilité d\'imprimer les documents numériques.\n 0: non \n 1: oui', 'h_cart', 0),
(1009, 'pmb', 'allow_authorities_first_page', '1', 'Active ou non l\'affichage par défaut la première page d\'une liste d\'autorité lorsque l\'on arrive en sélection dans un popup.\n 0: non \n 1:Oui', '', 0),
(1010, 'pmb', 'pret_already_borrowed', '0', 'Autoriser le prêt d\'un exemplaire déjà prêté.\n 0: non \n 1:Oui', '', 0),
(1011, 'exportparam', 'export_horizontale', '0', 'Lien vers les notices liées horizontales', '', 1),
(1012, 'exportparam', 'export_notice_horizontale_link', '0', 'Exporter les notices liées horizontales', '', 1),
(1013, 'opac', 'exp_export_horizontale', '0', 'Lien vers les notices liées horizontales', '', 1),
(1014, 'opac', 'exp_export_notice_horizontale_link', '0', 'Exporter les notices liées horizontales', '', 1),
(1015, 'semantic', 'active', '0', 'Module \"Sémantique\" activé.\n 0: non \n 1:Oui', '', 0),
(1016, 'opac', 'focus_user_query', '1', 'Activer le focus sur le champ de recherche.\n 0: non \n 1:Oui', 'c_recherche', 0),
(1017, 'transferts', 'edition_show_all_colls', '0', 'Afficher dans tous les cas la source et la destination en édition de transfert', '', 1),
(1018, 'dsi', 'private_bannette_nb_notices', '30', 'Nombre maximum par défaut de notices diffusées dans les bannettes privées.', '', 0),
(1019, 'pmb', 'entity_graph_activate', '0', 'Active ou non le graphe des entités PMB.\n 0: Non \n 1: Oui', '', 0),
(1020, 'pmb', 'entity_graph_recursion_lvl', '1', 'Valeur numérique définissant le niveau de profondeur du graphe.', '', 0),
(1021, 'pmb', 'contribution_area_activate', '0', 'Espace de contribution actif: \r\n0: Non\r\n1: Oui', '', 0),
(1022, 'opac', 'contribution_area_activate', '0', 'Espace de contribution actif ? \n0 : Non\n1 : Oui\n', 'f_modules', 0),
(1023, 'gestion_acces', 'empr_contribution_area', '0', 'Gestion des droits d\'accès des emprunteurs aux espaces de contribution\n0 : Non.\n1 : Oui.', '', 0),
(1024, 'gestion_acces', 'empr_contribution_area_def', '0', 'Valeur par défaut en modification d\'espaces de contribution pour les droits d\'accès emprunteurs - espaces de contribution \n0 : Recalculer.\n1 : Choisir.', '', 0),
(1025, 'empr', 'visits_statistics_active', '0', 'Activer les statistiques de fréquentation.\n 0 : Non \n 1 : Oui', '', 0),
(1026, 'pmb', 'contribution_ws_username', '', 'Paramètre caché contenant le nom d\'utilisateur du ws', '', 1),
(1027, 'pmb', 'contribution_ws_password', '', 'Paramètre caché contenant le mot de passe de l\'utilisateur du ws', '', 1),
(1028, 'pmb', 'contribution_ws_url', '', 'Paramètre caché contenant l\'url du ws', '', 1),
(1029, 'pmb', 'explnum_controle_doublons', '0', 'Contrôle sur les doublons de documents numérique.\n 0 : Aucun dédoublonnage\n 1 : Dédoublonnage sur le contenu des documents numériques', '', 0),
(1030, 'opac', 'integrate_anonymous_cart', '1', 'Proposer le transfert des éléments du panier lors de l\'authentification.\n 0 : Non\n 1 : Sur demande', 'h_cart', 0),
(1031, 'opac', 'print_email_autocomplete', '0', 'Autoriser la complétion de l\'adresse mail sur le formulaire d\'impression de recherche\n 0 : Non \n 1 : Seulement pour les lecteurs connectés \n 2 : Pour tous les lecteurs', 'a_general', 0),
(1032, 'opac', 'simplified_cart', '0', 'Affichage simplifié du panier.\n 0 : non \n 1 : oui', 'h_cart', 0),
(1033, 'opac', 'scan_request_send_mail_status', '', 'Envoi d\'email au destinataire sur passage de la demande à ces statuts', 'a_general', 1),
(1034, 'empr', 'filter_relance_rows', 'g,cs', 'Critères de filtrage ajoutés aux critères existants pour les relances à faire, saisir les critères séparés par des virgules.\nLes critères disponibles correspondent à l\'attribut value du fichier substituable empr_list.xml', '', 0),
(1035, 'pmb', 'authority_img_folder_id', '0', 'Identifiant du répertoire d\'upload des vignettes d\'autorités', '', 0),
(1036, 'pmb', 'authority_img_pics_max_size', '150', 'Taille maximale des vignettes uploadées dans les autorités, en largeur ou en hauteur', '', 0),
(1037, 'pmb', 'notice_author_functions_grouping', '1', 'Regrouper les fonctions d\'auteur en affichage de notice ? \n0 : Non.\n1 : Oui.', '', 0),
(1038, 'mailretard', 'hide_fine', '0', 'Masquer les amendes et frais de relance dans les lettres et mails de retard :\n 0 : Non\n 1 : Oui', '', 0),
(1039, 'mailretard', 'priorite_email_2', '0', 'Forcer le deuxième niveau de relance par lettre si priorite_email = 1 :\n 0 : Non\n 1 : Oui', '', 0),
(1040, 'pmb', 'utiliser_calendrier_location', '0', 'Si le paramètre utiliser_calendrier est à 1, choix de la localisation pour calculer le retard, l\'amende et le blocage :\n 0 : calcul sur le calendrier d\'ouverture de la localisation de l\'utilisateur\n 1 : calcul sur le calendrier d\'ouverture de la localisation de l\'exemplaire', '', 0),
(1041, 'pmb', 'contribution_opac_show_sub_form', '', 'Paramètre caché indiquant l\'affichage ou non des sous-formulaires de contribution', '', 1),
(1043, 'pmb', 'img_cache_url', '', 'URL d\'accès du répertoire du cache des images (img_cache_folder)', '', 0),
(1044, 'opac', 'img_cache_folder', '', 'Répertoire de stockage du cache des images', 'a_general', 0),
(1045, 'opac', 'img_cache_url', '', 'URL d\'accès du répertoire du cache des images (img_cache_folder)', 'a_general', 0),
(1046, 'sphinx', 'active', '0', 'Sphinx activé.\n 0 : Non\n 1 : Oui', '', 0),
(1047, 'sphinx', 'indexes_path', '', 'Chemin vers le répertoire de stockage des index sphinx', '', 0),
(1048, 'sphinx', 'mysql_connect', '127.0.0.1:9306,0', 'Paramètre de connexion mysql au serveur sphinx :\n hote:port,auth,user,pass : mettre 0 ou 1 pour l\'authentification.', '', 0),
(1049, 'pmb', 'map_bounding_box', '-5 50,9 50,9 40,-5 40,-5 50', 'Zone d\'affichage par défaut de la carte. Coordonnées d\'un polygone fermé, en degrés décimaux', 'map', 0),
(1050, 'opac', 'map_bounding_box', '-5 50,9 50,9 40,-5 40,-5 50', 'Zone d\'affichage par défaut de la carte. Coordonnées d\'un polygone fermé, en degrés décimaux', 'map', 0),
(1051, 'gestion_acces', 'empr_contribution_scenario', '0', 'Gestion des droits d\'accès des emprunteurs aux scénarios de contribution\n0 : Non.\n1 : Oui.', '', 0),
(1052, 'gestion_acces', 'empr_contribution_scenario_def', '0', 'Valeur par défaut en modification de scenario de contribution pour les droits d\'accès emprunteurs - scénarios\n0 : Recalculer.\n1 : Choisir.', '', 0),
(1053, 'gestion_acces', 'contribution_moderator_empr', '0', 'Gestion des droits d\'accès des modérateurs sur les contributeurs\n0 : Non.\n1 : Oui.', '', 0),
(1054, 'gestion_acces', 'contribution_moderator_empr_def', '0', 'Valeur par défaut en modification d\'emprunteur pour les droits d\'accès modérateur - emprunteur\n0 : Recalculer.\n1 : Choisir.', '', 0),
(1055, 'opac', 'print_cart_header_footer', '', 'Identifiant du template à utiliser pour insérer un en-tête et un pied de page en impression de panier. Les templates sont créés en Administration > Template de Mail > Template impression de panier.', 'h_cart', 0),
(1056, 'pmb', 'dojo_gestion_style', 'claro', 'Styles disponibles: tundra, claro, flat, nihilo, soria', '', 0),
(1057, 'pmb', 'popup_form_display_mode', '1', 'Mode d\'affichage par défaut du formulaire de création dans une popup de sélection.\n 1 : Simple \n 2 : Avancé', '', 0),
(1058, 'pmb', 'indexation_in_progress', '0', 'Paramètre caché permettant de définir si une indexation est en cours', '', 1),
(1059, 'pmb', 'indexation_needed', '0', 'Paramètre caché permettant de définir si une indexation est nécessaire', '', 1),
(1060, 'thesaurus', 'concepts_autopostage', '0', 'Activer l\'autopostage dans les concepts. \n 0 : Non, \n 1 : Oui', 'concepts', 0),
(1061, 'thesaurus', 'concepts_autopostage_generic_levels_nb', '3', 'Nombre de niveaux de recherche dans les concepts génériques. \n * : Tous, \n n : nombre de niveaux', 'concepts', 0),
(1062, 'thesaurus', 'concepts_autopostage_specific_levels_nb', '3', 'Nombre de niveaux de recherche dans les concepts spécifiques. \n * : Tous, \n n : nombre de niveaux', 'concepts', 0),
(1063, 'opac', 'concepts_autopostage', '0', 'Activer l\'autopostage dans les concepts. Le nombre de niveaux vers génériques et/ou spécifiques est défini par les paramètres de gestion . \n 0 : Non, \n 1 : Oui', 'c_recherche', 0),
(1064, 'frbr', 'active', '0', 'Module \'FRBR\' activé.\n 0 : Non.\n 1 : Oui.', '', 0),
(1065, 'modelling', 'active', '0', 'Module \'Modélisation\' activé.\n 0 : Non.\n 1 : Oui.', '', 0),
(1066, 'pmb', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui', 'debug', 0),
(1067, 'opac', 'display_errors', '0', 'Afficher les erreurs PHP ? \n 0 : Non \n 1 : Oui', 'debug', 0),
(1068, 'opac', 'perio_a2z_default_active_subscription_filter', '0', 'Filtre sur les abonnements actifs coché par défaut dans le navigateur de périodiques ?\n 0 : Non\n 1 : Oui', 'c_recherche', 0),
(1069, 'pmb', 'printer_list', '', 'Liste des imprimantes de ticket de prêt gérées par raspberry, séparées par un point-virgule. Indiquer un identifiant, un libellé et une IP de raspberry (facultative) alternative à celle du paramètre général printer_name.\nExemple : 1_Imprimante prêt;2_Autre imprimante(192.168.0.83:3000).', '', 0),
(1070, 'opac', 'search_universes_activate', '0', 'Univers de recherche activés : \r\n0: Non\r\n1: Oui', 'c_recherche', 0),
(1071, 'pmb', 'pnb_param_login', '', 'Paramétrage du Login de PNB.', '', 1),
(1072, 'pmb', 'pnb_param_password', '', 'Paramétrage du mot de passe de PNB.', '', 1),
(1073, 'pmb', 'pnb_param_ftp_login', '', 'Paramétrage du login du FTP de PNB.', '', 1),
(1074, 'pmb', 'pnb_param_ftp_password', '', 'Paramétrage du mot de passe du FTP de PNB.', '', 1),
(1075, 'pmb', 'pnb_param_ftp_server', '', 'Paramétrage de l\'url du FTP de PNB.', '', 1),
(1076, 'pmb', 'pnb_param_ws_user_name', '', 'Paramétrage du nom de l\'utilisateur externe.', '', 1),
(1077, 'pmb', 'pnb_param_ws_user_password', '', 'Paramétrage du mot de passe de l\'utilisateur externe.', '', 1),
(1078, 'pmb', 'pnb_param_dilicom_url', '', 'Paramétrage de l\'url du webservice Dilicom.', '', 1),
(1079, 'opac', 'pnb_param_webservice_url', '', 'Paramétrage de l\'url du webservice de gestion pour les prêts numériques.', '', 1),
(1080, 'pmb', 'pnb_loan_counter', '0', 'Paramètre caché contenant le compteur de prêt numérique', '', 1),
(1081, 'dsi', 'private_bannette_search_equation', '0', 'Id de l\'équation de recherche utilisée par défaut en complément de l\'équation privée en diffusion de bannettes privées.', '', 0),
(1082, 'pmb', 'enable_explnum_edition_popup', '0', 'Activation de l\'édition des documents numériques en popup ?\n0 : Non\n1 : Oui', '', 0),
(1083, 'pmb', 'pnb_drm_parameters', '0', 'Mémorise les informations des DRM du PNB', '', 1),
(1084, 'pmb', 'pnb_alert_end_offers', '0', 'Nombre de jours entre le déclanchement de l\'alerte et l\'expiration des commandes', '', 1),
(1085, 'pmb', 'pnb_alert_staturation_offers', '0', 'Nombre d\'exemplaires restants avant le déclanchement de l\'alerte pour les commandes arrivant à saturation de prêt', '', 1),
(1086, 'pmb', 'pnb_clean_loans_date', '2022-11-28', 'Date du nettoyage des prêts PNB expirés', '', 1),
(1087, 'pmb', 'session_reactivate', '', 'Durée maximale de la session sans rafraîchissement (en secondes). Si vide ou 0, valeur fixée à 120 minutes', '', 0),
(1088, 'pmb', 'session_maxtime', '', 'Durée maximale de la session (en secondes). Si vide ou 0, valeur fixée à 24 heures', '', 0),
(1089, 'pmb', 'explnum_order', 'explnum_mimetype, explnum_nom, explnum_id', 'Ordre d\'affichage des documents numériques, dans l\'ordre donné, séparé par des virgules : explnum_mimetype, explnum_nom, explnum_id', '', 0),
(1090, 'acquisition', 'sugg_to_cde_resa_auto', '1', 'Création automatique d\'une réservation lors de la réception d\'une ligne de commande liée à une suggestion d\'emprunteur.\nLa réservation sur les notices sans exemplaires (Paramètres généraux > resa_records_no_expl) doit être activée pour cela.\n Non : 0 \n Oui : 1', '', 0),
(1091, 'acquisition', 'type_produit', '0', 'Utilisation d\'un type de produit pour les commandes.\n 0:optionnel\n 1:obligatoire', '', 0),
(1092, 'opac', 'private_bannette_date_used_to_calc', '0', 'Date des notices à utiliser en diffusion de bannettes privées ? 0 : date de création, 1 : date de modification, 2 : Sélectionnable par l\'usager en OPAC', 'l_dsi', 0),
(1093, 'sphinx', 'pert_calc_method', '(sum(lcs*user_weight)+top(exact_order*user_weight/min_hit_pos)+top(3*exact_hit*user_weight))*1000+bm25', 'Méthode de calcul de la pertinence pour Sphinx en mode expression (par défaut dans PMB). \n La liste des facteurs disponibles : http://sphinxsearch.com/docs/current.html#expression-ranker \n Exemples des autre modes : \n SPH_RANK_PROXIMITY_BM25 = sum(lcs*user_weight)*1000+bm25 \n SPH_RANK_BM25 = bm25 \n SPH_RANK_WORDCOUNT = sum(hit_count*user_weight) \n SPH_RANK_PROXIMITY = sum(lcs*user_weight) \n SPH_RANK_MATCHANY = sum((word_count+(lcs-1)*max_lcs)*user_weight) \n SPH_RANK_FIELDMASK = field_mask \n SPH_RANK_SPH04 = sum((4*lcs+2*(min_hit_pos==1)+exact_hit)*user_weight)*1000+bm25', '', 0),
(1094, 'pmb', 'entity_locked_time', '0', 'Temps de verrouillage des entités en minutes. \n 0: aucun verrouillage, \n ## : durée de blocage de l\'entité après enregistrement ou abandon de la modification. Conseillé 5. Permet d\'éviter les entités restées verrouillées.', '', 0),
(1095, 'pmb', 'entity_locked_refresh_time', '5', 'Paramètre système contenant le temps de rafraichissement de la date de dernier accès à une entité (en minute)', '', 0),
(1096, 'pmb', 'show_exemplaires_analysis', '0', 'Afficher les exemplaires du bulletin sous l\'article affiché ? \n 0: Non \n 1: Oui', '', 0),
(1097, 'dsi', 'connexion_auto', '1', 'Connexion automatique de l\'usager à l\'OPAC à partir d\'un mail de la DSI activée ? \n 0: Non \n 1: Oui', '', 0),
(1098, 'pmb', 'url_internal', 'http://SERVER/DIRECTORY/', 'URL interne utilisée quand le serveur doit s\'appeler lui même. Ne pas oublier le / final', '', 0),
(1099, 'opac', 'connexion_auto_duration', '0', 'Durée de validité (en heures) du lien de connexion automatique', 'l_dsi', 0),
(1100, 'pmb', 'vedette_objects_id_updated', '2', 'La mise à jour des constantes de vedette a-t-elle été faite ?\n 0: Non\n 1: En cours\n 2: Terminée', '', 1),
(1101, 'pmb', 'controle_doublons_diacrit', '0', 'Prendre en compte les diacritiques dans le dédoublonnage des autorités auteurs et éditeurs? \n 0 : Non \n 1 : Oui', '', 0),
(1102, 'opac', 'allow_extended_search_authorities', '0', 'Autorisation ou non de la recherche avancée dans les autorités\n 0 : Non \n 1 : Oui', 'c_recherche', 0),
(1103, 'cms', 'editorial_form_editables', '1', 'Grilles éditables sur les articles et rubriques du contenu éditorial ? \n 0 non \n 1 oui', '', 0),
(1104, 'opac', 'scan_request_location_activate', '0', 'Activer la localisation d\'une demande de numérisation', 'f_modules', 0),
(1105, 'exportparam', 'export_map', '0', 'Exporter les emprises cartographiques des notices', '', 1),
(1106, 'opac', 'exp_export_map', '0', 'Exporter les emprises cartographiques des notices', '', 1),
(1108, 'opac', 'short_url_rss_records_format', '1', 'Format d\'affichage des notices du flux RSS de recherche\n 1: Défaut\n H 1 = id d\'un template de notice', 'd_aff_recherche', 0),
(1109, 'empr', 'active_opac_renewal', '0', 'Activer la prolongation d\'abonnement à l\'OPAC', '', 0),
(1110, 'empr', 'opac_account_deleted_status', '0', '0 : Bouton de suppression du compte lecteur en OPAC invisible\nn : Identifiant du statut pour la suppression du compte lecteur + bouton visible', '', 0),
(1111, 'opac', 'default_sort_reading', 'd_text_35', 'Tri par défaut des recherches OPAC.\nDe la forme, c_num_6 (c pour croissant, d pour décroissant, puis num ou text pour numérique ou texte et enfin l\'identifiant du champ (voir fichier xml sort.xml))', 'd_aff_recherche', 0),
(1112, 'opac', 'default_sort_reading_list', '1 d_text_14|Trier par date', 'Listes de lecture :\nAfficher la liste déroulante de sélection d\'un tri ?\n 0 : Non\n 1 : Oui\nFaire suivre d\'un espace pour l\'ajout de plusieurs tris sous la forme : c_num_6|Libelle||d_text_7|Libelle 2||c_num_5|Libelle 3\n\nc pour croissant, d pour décroissant\nnum ou text pour numérique ou texte\nidentifiant du champ (voir fichier xml sort.xml)\nlibellé du tri (optionnel)', 'd_aff_recherche', 0),
(1113, 'dsi', 'send_empr_date_expiration', '1', 'Envoyer la D.S.I aux lecteurs dont la date de fin d\'adhésion est dépassée ?\n 0: Non\n 1: Oui', '', 0),
(1114, 'pmb', 'aut_link_autocompletion', '0', 'Activer l\'autocomplétion dans les liens entre autorités ?\n 0: Non\n 1: Oui', '', 0),
(1115, 'sphinx', 'indexes_prefix', '', 'Prefixe pour le nommage des index sphinx', '', 0),
(672, 'internal', 'emptylogstatopac', '0', 'Paramètre interne, ne pas modifier\r\n =1 si vidage des logs en cours', '', 0);

-- --------------------------------------------------------

--
-- Structure de la table `parametres_uncached`
--

CREATE TABLE IF NOT EXISTS `parametres_uncached` (
  `id_param` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_param` varchar(20) DEFAULT NULL,
  `sstype_param` varchar(255) DEFAULT NULL,
  `valeur_param` text DEFAULT NULL,
  `comment_param` longtext DEFAULT NULL,
  `section_param` varchar(255) NOT NULL DEFAULT '',
  `gestion` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_param`),
  UNIQUE KEY `typ_sstyp` (`type_param`,`sstype_param`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `parametres_uncached`
--

INSERT INTO `parametres_uncached` VALUES
(1, 'internal', 'emptylogstatopac', '0', 'Paramètre interne, ne pas modifier\r\n =1 si vidage des logs en cours', '', 0);

-- --------------------------------------------------------

--
-- Structure de la table `param_subst`
--

CREATE TABLE IF NOT EXISTS `param_subst` (
  `subst_module_param` varchar(20) NOT NULL DEFAULT '',
  `subst_module_num` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `subst_type_param` varchar(20) NOT NULL DEFAULT '',
  `subst_sstype_param` varchar(255) NOT NULL DEFAULT '',
  `subst_valeur_param` text NOT NULL,
  `subst_comment_param` longtext NOT NULL,
  PRIMARY KEY (`subst_module_param`,`subst_module_num`,`subst_type_param`,`subst_sstype_param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pclassement`
--

CREATE TABLE IF NOT EXISTS `pclassement` (
  `id_pclass` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_pclass` varchar(255) NOT NULL DEFAULT '',
  `typedoc` varchar(255) NOT NULL DEFAULT '',
  `locations` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_pclass`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `pclassement`
--

INSERT INTO `pclassement` VALUES
(1, 'Plan interne', 'abcdefgijklmr', '');

-- --------------------------------------------------------

--
-- Structure de la table `perio_relance`
--

CREATE TABLE IF NOT EXISTS `perio_relance` (
  `rel_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rel_abt_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rel_date_parution` date NOT NULL DEFAULT '0000-00-00',
  `rel_libelle_numero` varchar(255) DEFAULT NULL,
  `rel_comment_gestion` text NOT NULL,
  `rel_comment_opac` text NOT NULL,
  `rel_nb` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rel_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`rel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_agenda`
--

CREATE TABLE IF NOT EXISTS `personal_agenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(10) UNSIGNED DEFAULT NULL,
  `title` text DEFAULT NULL,
  `text` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `enddate` datetime DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `parent_event_id` int(11) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_agenda_repeat`
--

CREATE TABLE IF NOT EXISTS `personal_agenda_repeat` (
  `cal_id` int(11) NOT NULL DEFAULT 0,
  `cal_type` varchar(20) DEFAULT NULL,
  `cal_end` int(11) DEFAULT NULL,
  `cal_frequency` int(11) DEFAULT 1,
  `cal_days` char(7) DEFAULT NULL,
  PRIMARY KEY (`cal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_agenda_repeat_not`
--

CREATE TABLE IF NOT EXISTS `personal_agenda_repeat_not` (
  `cal_id` int(11) NOT NULL,
  `cal_date` int(11) NOT NULL,
  PRIMARY KEY (`cal_id`,`cal_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `php_session`
--

CREATE TABLE IF NOT EXISTS `php_session` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `session_name` varchar(10) NOT NULL DEFAULT '',
  `session_time` int(11) NOT NULL DEFAULT 0,
  `session_start` int(11) NOT NULL DEFAULT 0,
  `session_value` mediumtext NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `planificateur`
--

CREATE TABLE IF NOT EXISTS `planificateur` (
  `id_planificateur` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_type_tache` int(11) NOT NULL,
  `libelle_tache` varchar(255) NOT NULL,
  `desc_tache` varchar(255) DEFAULT NULL,
  `num_user` int(11) NOT NULL,
  `param` text DEFAULT NULL,
  `statut` tinyint(1) UNSIGNED DEFAULT 0,
  `rep_upload` int(8) DEFAULT NULL,
  `path_upload` text DEFAULT NULL,
  `perio_heure` varchar(28) DEFAULT NULL,
  `perio_minute` varchar(28) DEFAULT '01',
  `perio_jour_mois` varchar(128) DEFAULT '*',
  `perio_jour` varchar(128) DEFAULT NULL,
  `perio_mois` varchar(128) DEFAULT NULL,
  `calc_next_heure_deb` varchar(28) DEFAULT NULL,
  `calc_next_date_deb` date DEFAULT NULL,
  PRIMARY KEY (`id_planificateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pnb_loans`
--

CREATE TABLE IF NOT EXISTS `pnb_loans` (
  `id_pnb_loan` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pnb_loan_order_line_id` varchar(255) NOT NULL DEFAULT '',
  `pnb_loan_link` varchar(255) NOT NULL DEFAULT '',
  `pnb_loan_request_id` varchar(255) NOT NULL DEFAULT '',
  `pnb_loan_num_expl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_loan_num_loaner` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_loan_drm` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_pnb_loan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pnb_orders`
--

CREATE TABLE IF NOT EXISTS `pnb_orders` (
  `id_pnb_order` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pnb_order_id_order` varchar(255) NOT NULL DEFAULT '',
  `pnb_order_line_id` varchar(255) NOT NULL DEFAULT '',
  `pnb_order_num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_order_loan_max_duration` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_order_nb_loans` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_order_nb_simultaneous_loans` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_order_nb_consult_in_situ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_order_nb_consult_ex_situ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_order_offer_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pnb_order_offer_date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pnb_order_offer_duration` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pnb_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pnb_orders_expl`
--

CREATE TABLE IF NOT EXISTS `pnb_orders_expl` (
  `pnb_order_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pnb_order_expl_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`pnb_order_num`,`pnb_order_expl_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pret`
--

CREATE TABLE IF NOT EXISTS `pret` (
  `pret_idempr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pret_idexpl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pret_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pret_retour` date DEFAULT NULL,
  `pret_arc_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `niveau_relance` int(1) NOT NULL DEFAULT 0,
  `date_relance` date DEFAULT '0000-00-00',
  `printed` int(1) NOT NULL DEFAULT 0,
  `retour_initial` date DEFAULT '0000-00-00',
  `cpt_prolongation` int(1) NOT NULL DEFAULT 0,
  `pret_temp` varchar(50) NOT NULL DEFAULT '',
  `short_loan_flag` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`pret_idexpl`),
  KEY `i_pret_idempr` (`pret_idempr`),
  KEY `i_pret_arc_id` (`pret_arc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pret_archive`
--

CREATE TABLE IF NOT EXISTS `pret_archive` (
  `arc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `arc_debut` datetime DEFAULT '0000-00-00 00:00:00',
  `arc_fin` datetime DEFAULT NULL,
  `arc_id_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `arc_empr_cp` varchar(10) NOT NULL DEFAULT '',
  `arc_empr_ville` varchar(255) NOT NULL DEFAULT '',
  `arc_empr_prof` varchar(255) NOT NULL DEFAULT '',
  `arc_empr_year` int(4) UNSIGNED DEFAULT 0,
  `arc_empr_categ` smallint(5) UNSIGNED DEFAULT 0,
  `arc_empr_codestat` smallint(5) UNSIGNED DEFAULT 0,
  `arc_empr_sexe` tinyint(3) UNSIGNED DEFAULT 0,
  `arc_empr_statut` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `arc_empr_location` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `arc_type_abt` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `arc_expl_typdoc` int(5) UNSIGNED DEFAULT 0,
  `arc_expl_cote` varchar(255) NOT NULL DEFAULT '',
  `arc_expl_statut` smallint(5) UNSIGNED DEFAULT 0,
  `arc_expl_location` smallint(5) UNSIGNED DEFAULT 0,
  `arc_expl_location_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `arc_expl_location_retour` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `arc_expl_codestat` smallint(5) UNSIGNED DEFAULT 0,
  `arc_expl_owner` mediumint(8) UNSIGNED DEFAULT 0,
  `arc_expl_section` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `arc_expl_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `arc_expl_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `arc_expl_bulletin` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `arc_groupe` varchar(255) NOT NULL DEFAULT '',
  `arc_niveau_relance` int(1) UNSIGNED DEFAULT 0,
  `arc_date_relance` date NOT NULL DEFAULT '0000-00-00',
  `arc_printed` int(1) UNSIGNED DEFAULT 0,
  `arc_cpt_prolongation` int(1) UNSIGNED DEFAULT 0,
  `arc_short_loan_flag` int(1) NOT NULL DEFAULT 0,
  `arc_pnb_flag` int(1) NOT NULL DEFAULT 0,
  `arc_pret_source_device` varchar(255) NOT NULL DEFAULT '',
  `arc_retour_source_device` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`arc_id`),
  KEY `i_pa_expl_id` (`arc_expl_id`),
  KEY `i_pa_idempr` (`arc_id_empr`),
  KEY `i_pa_expl_notice` (`arc_expl_notice`),
  KEY `i_pa_expl_bulletin` (`arc_expl_bulletin`),
  KEY `i_pa_arc_fin` (`arc_fin`),
  KEY `i_pa_arc_empr_categ` (`arc_empr_categ`),
  KEY `i_pa_arc_expl_location` (`arc_expl_location`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pret_custom`
--

CREATE TABLE IF NOT EXISTS `pret_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `datatype` varchar(10) NOT NULL DEFAULT '',
  `options` text DEFAULT NULL,
  `multiple` int(11) NOT NULL DEFAULT 0,
  `obligatoire` int(11) NOT NULL DEFAULT 0,
  `ordre` int(11) DEFAULT NULL,
  `search` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `export` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `filters` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `exclusion_obligatoire` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(11) NOT NULL DEFAULT 100,
  `opac_sort` int(11) NOT NULL DEFAULT 0,
  `comment` blob NOT NULL DEFAULT '',
  `custom_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idchamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pret_custom_dates`
--

CREATE TABLE IF NOT EXISTS `pret_custom_dates` (
  `pret_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pret_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pret_custom_date_type` int(11) DEFAULT NULL,
  `pret_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `pret_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `pret_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`pret_custom_champ`,`pret_custom_origine`,`pret_custom_order`),
  KEY `pret_custom_champ` (`pret_custom_champ`),
  KEY `pret_custom_origine` (`pret_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pret_custom_lists`
--

CREATE TABLE IF NOT EXISTS `pret_custom_lists` (
  `pret_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pret_custom_list_value` varchar(255) DEFAULT NULL,
  `pret_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `i_pret_custom_champ` (`pret_custom_champ`),
  KEY `i_pret_champ_list_value` (`pret_custom_champ`,`pret_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pret_custom_values`
--

CREATE TABLE IF NOT EXISTS `pret_custom_values` (
  `pret_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pret_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pret_custom_small_text` varchar(255) DEFAULT NULL,
  `pret_custom_text` text DEFAULT NULL,
  `pret_custom_integer` int(11) DEFAULT NULL,
  `pret_custom_date` date DEFAULT NULL,
  `pret_custom_float` float DEFAULT NULL,
  `pret_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `i_pret_custom_champ` (`pret_custom_champ`),
  KEY `i_pret_custom_origine` (`pret_custom_origine`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `print_cart_tpl`
--

CREATE TABLE IF NOT EXISTS `print_cart_tpl` (
  `id_print_cart_tpl` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `print_cart_tpl_name` varchar(255) NOT NULL DEFAULT '',
  `print_cart_tpl_header` text NOT NULL,
  `print_cart_tpl_footer` text NOT NULL,
  PRIMARY KEY (`id_print_cart_tpl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `procs`
--

CREATE TABLE IF NOT EXISTS `procs` (
  `idproc` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `requete` blob NOT NULL,
  `comment` tinytext NOT NULL,
  `autorisations` mediumtext DEFAULT NULL,
  `autorisations_all` int(1) NOT NULL DEFAULT 0,
  `parameters` text DEFAULT NULL,
  `num_classement` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `proc_notice_tpl` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `proc_notice_tpl_field` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idproc`),
  KEY `idproc` (`idproc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `procs_classements`
--

CREATE TABLE IF NOT EXISTS `procs_classements` (
  `idproc_classement` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libproc_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idproc_classement`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `publishers`
--

CREATE TABLE IF NOT EXISTS `publishers` (
  `ed_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ed_name` varchar(255) NOT NULL DEFAULT '',
  `ed_adr1` varchar(255) NOT NULL DEFAULT '',
  `ed_adr2` varchar(255) NOT NULL DEFAULT '',
  `ed_cp` varchar(10) NOT NULL DEFAULT '',
  `ed_ville` varchar(96) NOT NULL DEFAULT '',
  `ed_pays` varchar(96) NOT NULL DEFAULT '',
  `ed_web` varchar(255) NOT NULL DEFAULT '',
  `index_publisher` text DEFAULT NULL,
  `ed_comment` text DEFAULT NULL,
  `ed_num_entite` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`ed_id`),
  KEY `ed_name` (`ed_name`),
  KEY `ed_ville` (`ed_ville`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `publisher_custom`
--

CREATE TABLE IF NOT EXISTS `publisher_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `datatype` varchar(10) NOT NULL DEFAULT '',
  `options` text DEFAULT NULL,
  `multiple` int(11) NOT NULL DEFAULT 0,
  `obligatoire` int(11) NOT NULL DEFAULT 0,
  `ordre` int(11) DEFAULT NULL,
  `search` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `export` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `exclusion_obligatoire` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(11) NOT NULL DEFAULT 100,
  `opac_sort` int(11) NOT NULL DEFAULT 0,
  `comment` blob NOT NULL DEFAULT '',
  `custom_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idchamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `publisher_custom_dates`
--

CREATE TABLE IF NOT EXISTS `publisher_custom_dates` (
  `publisher_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `publisher_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `publisher_custom_date_type` int(11) DEFAULT NULL,
  `publisher_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `publisher_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `publisher_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`publisher_custom_champ`,`publisher_custom_origine`,`publisher_custom_order`),
  KEY `publisher_custom_champ` (`publisher_custom_champ`),
  KEY `publisher_custom_origine` (`publisher_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `publisher_custom_lists`
--

CREATE TABLE IF NOT EXISTS `publisher_custom_lists` (
  `publisher_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `publisher_custom_list_value` varchar(255) DEFAULT NULL,
  `publisher_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`publisher_custom_champ`),
  KEY `editorial_champ_list_value` (`publisher_custom_champ`,`publisher_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `publisher_custom_values`
--

CREATE TABLE IF NOT EXISTS `publisher_custom_values` (
  `publisher_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `publisher_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `publisher_custom_small_text` varchar(255) DEFAULT NULL,
  `publisher_custom_text` text DEFAULT NULL,
  `publisher_custom_integer` int(11) DEFAULT NULL,
  `publisher_custom_date` date DEFAULT NULL,
  `publisher_custom_float` float DEFAULT NULL,
  `publisher_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`publisher_custom_champ`),
  KEY `editorial_custom_origine` (`publisher_custom_origine`),
  KEY `i_pcv_st` (`publisher_custom_small_text`),
  KEY `i_pcv_t` (`publisher_custom_text`(255)),
  KEY `i_pcv_i` (`publisher_custom_integer`),
  KEY `i_pcv_d` (`publisher_custom_date`),
  KEY `i_pcv_f` (`publisher_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `quiz_answer_templates`
--

CREATE TABLE IF NOT EXISTS `quiz_answer_templates` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `question_id` mediumint(8) UNSIGNED NOT NULL,
  `answer` text NOT NULL,
  `correct` mediumint(8) UNSIGNED DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `ponderation` float(6,2) NOT NULL DEFAULT 0.00,
  `position` mediumint(8) UNSIGNED NOT NULL DEFAULT 1,
  `hotspot_coordinates` text DEFAULT NULL,
  `hotspot_type` enum('square','circle','poly','delineation') DEFAULT NULL,
  `destination` text NOT NULL,
  PRIMARY KEY (`id`,`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `quiz_answer_templates`
--

INSERT INTO `quiz_answer_templates` VALUES
(1, 4, '<div><font size=\"2\">Group Quarters : Any living quarters occupied by ten  or more [unrelated] persons is called a group quarters. Examples of a  group quarters are worker\'s dormitories, boading houses, halfway houses,  convents, etc. In addition, college [dormitories], fraternity houses,  or nurse\'s dormitories are [always] considered  to be a group quarters,  regardless of the [number] of students who live there.</font></div>\r\n<p> </p>::10,10,10,10@', 0, 'a:2:{s:10:\"comment[1]\";s:8:\"Correct.\";s:10:\"comment[2]\";s:16:\"Wrong! Try again\";}', 0.00, 0, '', '', ''),
(1, 5, 'Call ambulance', 0, 'Correct.', 0.00, 1, '', '', ''),
(1, 6, '', 1, '', 0.00, 1, '', '', ''),
(1, 7, '', NULL, '', 10.00, 1, '0;0|0|0', 'square', ''),
(2, 1, '140', 0, 'Wrong. Try again', 0.00, 2, '', '', '0@@0@@0@@0'),
(2, 2, 'A house in which a family or six and three boarders live', 1, 'Correct.', 3.33, 2, '', '', ''),
(2, 3, 'Montana', 0, 'Wrong. Try again.', 0.00, 2, '', '', ''),
(2, 5, 'Tell casualty not to move', 0, 'Wrong! Try again.', 0.00, 2, '', '', ''),
(3, 1, '239', 1, 'Correct. 239 is the only number not ending with \'40\'', 10.00, 3, '', '', '0@@0@@0@@0'),
(3, 2, 'A convent occupied by five nuns', 1, 'Correct.', 3.33, 3, '', '', ''),
(3, 3, 'Idaho', 0, 'Wrong. Try again.', 0.00, 3, '', '', ''),
(3, 5, 'Check skin temperature', 0, '', 0.00, 3, '', '', ''),
(4, 1, '340', 0, 'Wrong. Try again', 0.00, 4, '', '', '0@@0@@0@@0'),
(4, 2, 'A medical office building with eleven doctors\' officies', 1, 'Correct.', 3.33, 4, '', '', ''),
(4, 3, 'Oregon', 0, 'Wrong. Try again.', 0.00, 4, '', '', ''),
(4, 5, '1', 2, '', 0.00, 4, '', '', ''),
(5, 5, '2', 1, '', 0.00, 5, '', '', ''),
(6, 5, '3', 2, '', 0.00, 6, '', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `quiz_question_templates`
--

CREATE TABLE IF NOT EXISTS `quiz_question_templates` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `ponderation` float(6,2) NOT NULL DEFAULT 0.00,
  `position` mediumint(8) UNSIGNED NOT NULL DEFAULT 1,
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `picture` varchar(50) DEFAULT NULL,
  `level` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `image` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `position` (`position`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `quiz_question_templates`
--

INSERT INTO `quiz_question_templates` VALUES
(1, 'Which of the numbers below does not follow the pattern ...40, 140, 239, 340 ?10', '<table cellspacing=\"2\" cellpadding=\"0\" width=\"98%\" height=\"100%\" style=\"font-family: Comic Sans MS; font-size: 16px;\">\r\n    <tbody>\r\n        <tr>\r\n            <td align=\"center\" height=\"323px\"><img height=\"310px\" alt=\"\" src=\"../img/instructor-projection.png\" /></td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 10.00, 1, 1, '', 1, 'multiple_choice.png'),
(2, 'According to the definition below, which of the following is NOT a group quarters ? ', '<table cellspacing=\"2\" cellpadding=\"0\" width=\"98%\" height=\"100%\" style=\"font-family: Comic Sans MS; font-size: 16px;\">\r\n    <tbody>\r\n        <tr>\r\n            <td align=\"center\" height=\"323px\"><img height=\"310px\" src=\"../img/instructor-projection.png\" alt=\"\" /></td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 10.00, 2, 2, '', 1, 'multiple_answer.png'),
(3, 'What states does Columbia River run through ? Full sequence of correct answers must be right to get the score', '<table cellspacing=\"2\" cellpadding=\"0\" width=\"98%\" height=\"100%\" style=\"font-family: Comic Sans MS; font-size: 16px;\">\r\n    <tbody>\r\n        <tr>\r\n            <td align=\"center\" height=\"323px\"><img height=\"310px\" src=\"../img/instructor-projection.png\" alt=\"\" /></td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 0.00, 3, 8, '', 1, 'reasoning.png'),
(4, 'In the previous question, you were given the definition of \"Group Quarters\". Fill in the missing words.', '<table cellspacing=\"2\" cellpadding=\"0\" width=\"98%\" height=\"100%\" style=\"font-family: Comic Sans MS; font-size: 16px;\">\r\n    <tbody>\r\n        <tr>\r\n            <td align=\"center\" height=\"323px\"><img height=\"310px\" src=\"../img/instructor-projection.png\" alt=\"\" /></td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 40.00, 4, 3, '', 1, 'fill_in_the_blank.png'),
(5, 'On a car accident scene, in what sequence do you proceed to the following actions ?', '', 0.00, 5, 4, '', 1, 'drag_drop.png'),
(6, 'Explain the difference between a clinic and a hospital.', '<table cellspacing=\"2\" cellpadding=\"0\" width=\"98%\" height=\"100%\" style=\"font-family: Comic Sans MS; font-size: 16px;\">\r\n    <tbody>\r\n        <tr>\r\n            <td align=\"center\" height=\"323px\"><img height=\"310px\" src=\"../img/instructor-projection.png\" alt=\"\" /></td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 10.00, 6, 5, '', 1, 'open-question.png'),
(7, 'Identify each device of this computer.', '', 0.00, 7, 6, 'quiz-12.jpg', 1, 'dokeos_hotspots.png'),
(10, 'On a car accident scene, in what sequence do you proceed to the following actions ?', '', 0.00, 9, 4, '', 1, 'drag_drop.png'),
(11, 'Which of the numbers below does not follow the pattern ...40, 140, 239, 340 ?10', '<table cellspacing=\"2\" cellpadding=\"0\" width=\"98%\" height=\"100%\" style=\"font-family: Comic Sans MS; font-size: 16px;\">\r\n    <tbody>\r\n        <tr>\r\n            <td align=\"center\" height=\"323px\"><img height=\"310px\" alt=\"\" src=\"../img/instructor-projection.png\" /></td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 10.00, 1, 1, '', 1, 'multiple_choice.png'),
(12, 'Which of the numbers below does not follow the pattern ...40, 140, 239, 340 ?10', '<table cellspacing=\"2\" cellpadding=\"0\" width=\"98%\" height=\"100%\" style=\"font-family: Comic Sans MS; font-size: 16px;\">\r\n    <tbody>\r\n        <tr>\r\n            <td align=\"center\" height=\"323px\"><img height=\"310px\" alt=\"\" src=\"../img/instructor-projection.png\" /></td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 10.00, 1, 1, '', 1, 'multiple_choice.png'),
(13, 'Which of the numbers below does not follow the pattern ...40, 140, 239, 340 ?10', '<table cellspacing=\"2\" cellpadding=\"0\" width=\"98%\" height=\"100%\" style=\"font-family: Comic Sans MS; font-size: 16px;\">\r\n    <tbody>\r\n        <tr>\r\n            <td align=\"center\" height=\"323px\"><img height=\"310px\" alt=\"\" src=\"../img/instructor-projection.png\" /></td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 10.00, 1, 1, '', 1, 'multiple_choice.png');

-- --------------------------------------------------------

--
-- Structure de la table `quotas`
--

CREATE TABLE IF NOT EXISTS `quotas` (
  `quota_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `constraint_type` varchar(255) NOT NULL DEFAULT '',
  `elements` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `value` float DEFAULT NULL,
  PRIMARY KEY (`quota_type`,`constraint_type`,`elements`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `quotas_finance`
--

CREATE TABLE IF NOT EXISTS `quotas_finance` (
  `quota_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `constraint_type` varchar(255) NOT NULL DEFAULT '',
  `elements` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `value` float DEFAULT NULL,
  PRIMARY KEY (`quota_type`,`constraint_type`,`elements`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `quotas_opac_views`
--

CREATE TABLE IF NOT EXISTS `quotas_opac_views` (
  `quota_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `constraint_type` varchar(255) NOT NULL DEFAULT '',
  `elements` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `value` text NOT NULL,
  PRIMARY KEY (`quota_type`,`constraint_type`,`elements`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `quotas_pnb`
--

CREATE TABLE IF NOT EXISTS `quotas_pnb` (
  `quota_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `constraint_type` varchar(255) NOT NULL DEFAULT '',
  `elements` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `value` text NOT NULL,
  PRIMARY KEY (`quota_type`,`constraint_type`,`elements`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rapport_demandes`
--

CREATE TABLE IF NOT EXISTS `rapport_demandes` (
  `id_item` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `contenu` text NOT NULL,
  `num_note` int(10) NOT NULL DEFAULT 0,
  `num_demande` int(10) NOT NULL DEFAULT 0,
  `ordre` mediumint(3) NOT NULL DEFAULT 0,
  `type` mediumint(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rdfstore_g2t`
--

CREATE TABLE IF NOT EXISTS `rdfstore_g2t` (
  `g` mediumint(8) UNSIGNED NOT NULL,
  `t` mediumint(8) UNSIGNED NOT NULL,
  UNIQUE KEY `gt` (`g`,`t`),
  KEY `tg` (`t`,`g`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Structure de la table `rdfstore_id2val`
--

CREATE TABLE IF NOT EXISTS `rdfstore_id2val` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `misc` tinyint(1) NOT NULL DEFAULT 0,
  `val` text NOT NULL,
  `val_type` tinyint(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `id` (`id`,`val_type`),
  KEY `v` (`val`(64))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Structure de la table `rdfstore_index`
--

CREATE TABLE IF NOT EXISTS `rdfstore_index` (
  `num_triple` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subject_uri` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `subject_type` text NOT NULL,
  `predicat_uri` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `num_object` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `object_val` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `object_index` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `object_lang` char(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`num_object`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rdfstore_o2val`
--

CREATE TABLE IF NOT EXISTS `rdfstore_o2val` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `misc` tinyint(1) NOT NULL DEFAULT 0,
  `val_hash` char(32) NOT NULL,
  `val` text NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `vh` (`val_hash`),
  KEY `v` (`val`(64))
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Structure de la table `rdfstore_s2val`
--

CREATE TABLE IF NOT EXISTS `rdfstore_s2val` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `misc` tinyint(1) NOT NULL DEFAULT 0,
  `val_hash` char(32) NOT NULL,
  `val` text NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `vh` (`val_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Structure de la table `rdfstore_setting`
--

CREATE TABLE IF NOT EXISTS `rdfstore_setting` (
  `k` char(32) NOT NULL,
  `val` text NOT NULL,
  UNIQUE KEY `k` (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Structure de la table `rdfstore_triple`
--

CREATE TABLE IF NOT EXISTS `rdfstore_triple` (
  `t` mediumint(8) UNSIGNED NOT NULL,
  `s` mediumint(8) UNSIGNED NOT NULL,
  `p` mediumint(8) UNSIGNED NOT NULL,
  `o` mediumint(8) UNSIGNED NOT NULL,
  `o_lang_dt` mediumint(8) UNSIGNED NOT NULL,
  `o_comp` char(35) NOT NULL,
  `s_type` tinyint(1) NOT NULL DEFAULT 0,
  `o_type` tinyint(1) NOT NULL DEFAULT 0,
  `misc` tinyint(1) NOT NULL DEFAULT 0,
  UNIQUE KEY `t` (`t`),
  KEY `sp` (`s`,`p`),
  KEY `os` (`o`,`s`),
  KEY `po` (`p`,`o`),
  KEY `misc` (`misc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Structure de la table `recouvrements`
--

CREATE TABLE IF NOT EXISTS `recouvrements` (
  `recouvr_id` int(16) UNSIGNED NOT NULL AUTO_INCREMENT,
  `empr_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `id_expl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `date_rec` date NOT NULL DEFAULT '0000-00-00',
  `libelle` varchar(255) DEFAULT NULL,
  `montant` decimal(16,2) DEFAULT 0.00,
  `recouvr_type` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `date_pret` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_relance1` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_relance2` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_relance3` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`recouvr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rent_accounts`
--

CREATE TABLE IF NOT EXISTS `rent_accounts` (
  `id_account` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_num_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_num_exercice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_request_type` varchar(3) NOT NULL DEFAULT '',
  `account_type` varchar(3) NOT NULL DEFAULT '',
  `account_desc` text DEFAULT NULL,
  `account_date` datetime DEFAULT NULL,
  `account_receipt_limit_date` datetime DEFAULT NULL,
  `account_receipt_effective_date` datetime DEFAULT NULL,
  `account_return_date` datetime DEFAULT NULL,
  `account_num_uniform_title` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_title` varchar(255) NOT NULL DEFAULT '',
  `account_event_date` datetime DEFAULT NULL,
  `account_event_formation` varchar(255) NOT NULL DEFAULT '',
  `account_event_orchestra` varchar(255) NOT NULL DEFAULT '',
  `account_event_place` varchar(255) NOT NULL DEFAULT '',
  `account_num_publisher` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_num_supplier` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_num_author` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_num_pricing_system` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_percent` float(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `account_price` float(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `account_web` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `account_web_percent` float(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `account_web_price` float(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `account_comment` text DEFAULT NULL,
  `account_request_status` int(1) UNSIGNED NOT NULL DEFAULT 1,
  `account_num_acte` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rent_accounts_invoices`
--

CREATE TABLE IF NOT EXISTS `rent_accounts_invoices` (
  `account_invoice_num_account` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_invoice_num_invoice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`account_invoice_num_account`,`account_invoice_num_invoice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rent_account_types_sections`
--

CREATE TABLE IF NOT EXISTS `rent_account_types_sections` (
  `account_type_num_exercice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_type_num_section` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_type_marclist` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`account_type_num_section`,`account_type_marclist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rent_invoices`
--

CREATE TABLE IF NOT EXISTS `rent_invoices` (
  `id_invoice` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_num_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `invoice_date` datetime DEFAULT NULL,
  `invoice_status` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `invoice_valid_date` datetime DEFAULT NULL,
  `invoice_destination` varchar(10) NOT NULL DEFAULT '',
  `invoice_num_acte` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_invoice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rent_pricing_systems`
--

CREATE TABLE IF NOT EXISTS `rent_pricing_systems` (
  `id_pricing_system` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pricing_system_label` varchar(255) NOT NULL DEFAULT '',
  `pricing_system_desc` text DEFAULT NULL,
  `pricing_system_percents` text DEFAULT NULL,
  `pricing_system_num_exercice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pricing_system`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rent_pricing_system_grids`
--

CREATE TABLE IF NOT EXISTS `rent_pricing_system_grids` (
  `id_pricing_system_grid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pricing_system_grid_num_system` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pricing_system_grid_time_start` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pricing_system_grid_time_end` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pricing_system_grid_price` float(12,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `pricing_system_grid_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pricing_system_grid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resa`
--

CREATE TABLE IF NOT EXISTS `resa` (
  `id_resa` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `resa_idempr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resa_idnotice` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `resa_idbulletin` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `resa_date` datetime DEFAULT NULL,
  `resa_date_debut` date NOT NULL DEFAULT '0000-00-00',
  `resa_date_fin` date NOT NULL DEFAULT '0000-00-00',
  `resa_cb` varchar(255) NOT NULL DEFAULT '',
  `resa_confirmee` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `resa_loc_retrait` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `resa_arc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resa_planning_id_resa` int(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_resa`),
  KEY `resa_date_fin` (`resa_date_fin`),
  KEY `resa_date` (`resa_date`),
  KEY `resa_cb` (`resa_cb`),
  KEY `i_idbulletin` (`resa_idbulletin`),
  KEY `i_idnotice` (`resa_idnotice`),
  KEY `i_resa_idempr` (`resa_idempr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resa_archive`
--

CREATE TABLE IF NOT EXISTS `resa_archive` (
  `resarc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `resarc_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `resarc_debut` date NOT NULL DEFAULT '0000-00-00',
  `resarc_fin` date NOT NULL DEFAULT '0000-00-00',
  `resarc_idnotice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resarc_idbulletin` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resarc_confirmee` int(1) UNSIGNED DEFAULT 0,
  `resarc_cb` varchar(14) NOT NULL DEFAULT '',
  `resarc_loc_retrait` smallint(5) UNSIGNED DEFAULT 0,
  `resarc_from_opac` int(1) UNSIGNED DEFAULT 0,
  `resarc_anulee` int(1) UNSIGNED DEFAULT 0,
  `resarc_pretee` int(1) UNSIGNED DEFAULT 0,
  `resarc_arcpretid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resarc_id_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resarc_empr_cp` varchar(10) NOT NULL DEFAULT '',
  `resarc_empr_ville` varchar(255) NOT NULL DEFAULT '',
  `resarc_empr_prof` varchar(255) NOT NULL DEFAULT '',
  `resarc_empr_year` int(4) UNSIGNED DEFAULT 0,
  `resarc_empr_categ` smallint(5) UNSIGNED DEFAULT 0,
  `resarc_empr_codestat` smallint(5) UNSIGNED DEFAULT 0,
  `resarc_empr_sexe` tinyint(3) UNSIGNED DEFAULT 0,
  `resarc_empr_location` int(6) UNSIGNED NOT NULL DEFAULT 1,
  `resarc_expl_nb` int(5) UNSIGNED DEFAULT 0,
  `resarc_expl_typdoc` int(5) UNSIGNED DEFAULT 0,
  `resarc_expl_cote` varchar(255) NOT NULL DEFAULT '',
  `resarc_expl_statut` smallint(5) UNSIGNED DEFAULT 0,
  `resarc_expl_location` smallint(5) UNSIGNED DEFAULT 0,
  `resarc_expl_codestat` smallint(5) UNSIGNED DEFAULT 0,
  `resarc_expl_owner` mediumint(8) UNSIGNED DEFAULT 0,
  `resarc_expl_section` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `resarc_resa_planning_id_resa` int(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`resarc_id`),
  KEY `i_pa_idempr` (`resarc_id_empr`),
  KEY `i_pa_notice` (`resarc_idnotice`),
  KEY `i_pa_bulletin` (`resarc_idbulletin`),
  KEY `i_pa_resarc_date` (`resarc_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resa_loc`
--

CREATE TABLE IF NOT EXISTS `resa_loc` (
  `resa_loc` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `resa_emprloc` int(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`resa_loc`,`resa_emprloc`),
  KEY `i_resa_emprloc` (`resa_emprloc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resa_planning`
--

CREATE TABLE IF NOT EXISTS `resa_planning` (
  `id_resa` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `resa_idempr` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `resa_idnotice` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `resa_idbulletin` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `resa_date` datetime DEFAULT NULL,
  `resa_date_debut` date NOT NULL DEFAULT '0000-00-00',
  `resa_date_fin` date NOT NULL DEFAULT '0000-00-00',
  `resa_validee` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `resa_confirmee` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `resa_loc_retrait` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `resa_qty` int(5) UNSIGNED NOT NULL DEFAULT 1,
  `resa_remaining_qty` int(5) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_resa`),
  KEY `resa_date_fin` (`resa_date_fin`),
  KEY `resa_date` (`resa_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resa_ranger`
--

CREATE TABLE IF NOT EXISTS `resa_ranger` (
  `resa_cb` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`resa_cb`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation_category`
--

CREATE TABLE IF NOT EXISTS `reservation_category` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation_category_rights`
--

CREATE TABLE IF NOT EXISTS `reservation_category_rights` (
  `category_id` int(11) NOT NULL DEFAULT 0,
  `class_id` int(11) NOT NULL DEFAULT 0,
  `m_items` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation_item`
--

CREATE TABLE IF NOT EXISTS `reservation_item` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `course_code` varchar(40) NOT NULL DEFAULT '',
  `name` varchar(128) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `blackout` tinyint(4) NOT NULL DEFAULT 0,
  `creator` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `always_available` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation_item_rights`
--

CREATE TABLE IF NOT EXISTS `reservation_item_rights` (
  `item_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `class_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `edit_right` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `delete_right` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `m_reservation` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `view_right` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`item_id`,`class_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation_main`
--

CREATE TABLE IF NOT EXISTS `reservation_main` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `item_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `auto_accept` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `max_users` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `start_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subscribe_from` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subscribe_until` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subscribers` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notes` text NOT NULL,
  `timepicker` tinyint(4) NOT NULL DEFAULT 0,
  `timepicker_min` int(11) NOT NULL DEFAULT 0,
  `timepicker_max` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation_subscription`
--

CREATE TABLE IF NOT EXISTS `reservation_subscription` (
  `dummy` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `reservation_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `accepted` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `start_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`dummy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `responsability`
--

CREATE TABLE IF NOT EXISTS `responsability` (
  `id_responsability` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `responsability_author` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `responsability_notice` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `responsability_fonction` varchar(4) NOT NULL DEFAULT '',
  `responsability_type` mediumint(1) UNSIGNED NOT NULL DEFAULT 0,
  `responsability_ordre` smallint(2) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_responsability`,`responsability_author`,`responsability_notice`,`responsability_fonction`),
  KEY `responsability_notice` (`responsability_notice`),
  KEY `i_responsability_author` (`responsability_author`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `responsability_tu`
--

CREATE TABLE IF NOT EXISTS `responsability_tu` (
  `id_responsability_tu` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `responsability_tu_author_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `responsability_tu_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `responsability_tu_fonction` char(4) NOT NULL DEFAULT '',
  `responsability_tu_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `responsability_tu_ordre` smallint(2) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_responsability_tu`,`responsability_tu_author_num`,`responsability_tu_num`,`responsability_tu_fonction`),
  KEY `responsability_tu_author` (`responsability_tu_author_num`),
  KEY `responsability_tu_num` (`responsability_tu_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rss_content`
--

CREATE TABLE IF NOT EXISTS `rss_content` (
  `rss_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rss_content` longblob NOT NULL,
  `rss_content_parse` longblob NOT NULL,
  `rss_last` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`rss_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rss_flux`
--

CREATE TABLE IF NOT EXISTS `rss_flux` (
  `id_rss_flux` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_rss_flux` varchar(255) NOT NULL DEFAULT '',
  `link_rss_flux` blob NOT NULL,
  `descr_rss_flux` blob NOT NULL,
  `lang_rss_flux` varchar(255) NOT NULL DEFAULT 'fr',
  `copy_rss_flux` blob NOT NULL,
  `editor_rss_flux` varchar(255) NOT NULL DEFAULT '',
  `webmaster_rss_flux` varchar(255) NOT NULL DEFAULT '',
  `ttl_rss_flux` int(9) UNSIGNED NOT NULL DEFAULT 60,
  `img_url_rss_flux` blob NOT NULL,
  `img_title_rss_flux` blob NOT NULL,
  `img_link_rss_flux` blob NOT NULL,
  `format_flux` blob NOT NULL,
  `rss_flux_content` longblob NOT NULL,
  `rss_flux_last` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `export_court_flux` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `tpl_rss_flux` int(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_rss_flux`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rss_flux_content`
--

CREATE TABLE IF NOT EXISTS `rss_flux_content` (
  `num_rss_flux` int(9) UNSIGNED NOT NULL DEFAULT 0,
  `type_contenant` char(3) NOT NULL DEFAULT 'BAN',
  `num_contenant` int(9) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_rss_flux`,`type_contenant`,`num_contenant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rubriques`
--

CREATE TABLE IF NOT EXISTS `rubriques` (
  `id_rubrique` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_budget` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_parent` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `libelle` varchar(255) NOT NULL DEFAULT '',
  `commentaires` text NOT NULL,
  `montant` float(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `num_cp_compta` varchar(255) NOT NULL DEFAULT '',
  `autorisations` mediumtext NOT NULL,
  PRIMARY KEY (`id_rubrique`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sauv_lieux`
--

CREATE TABLE IF NOT EXISTS `sauv_lieux` (
  `sauv_lieu_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sauv_lieu_nom` varchar(50) DEFAULT NULL,
  `sauv_lieu_url` varchar(255) DEFAULT NULL,
  `sauv_lieu_protocol` varchar(10) DEFAULT 'file',
  `sauv_lieu_host` varchar(255) DEFAULT NULL,
  `sauv_lieu_login` varchar(255) DEFAULT NULL,
  `sauv_lieu_password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sauv_lieu_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sauv_log`
--

CREATE TABLE IF NOT EXISTS `sauv_log` (
  `sauv_log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sauv_log_start_date` date DEFAULT NULL,
  `sauv_log_file` varchar(255) DEFAULT NULL,
  `sauv_log_succeed` int(11) DEFAULT 0,
  `sauv_log_messages` mediumtext DEFAULT NULL,
  `sauv_log_userid` int(11) DEFAULT NULL,
  PRIMARY KEY (`sauv_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sauv_sauvegardes`
--

CREATE TABLE IF NOT EXISTS `sauv_sauvegardes` (
  `sauv_sauvegarde_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sauv_sauvegarde_nom` varchar(50) DEFAULT NULL,
  `sauv_sauvegarde_file_prefix` varchar(20) DEFAULT NULL,
  `sauv_sauvegarde_tables` mediumtext DEFAULT NULL,
  `sauv_sauvegarde_lieux` mediumtext DEFAULT NULL,
  `sauv_sauvegarde_users` mediumtext DEFAULT NULL,
  `sauv_sauvegarde_compress` int(11) DEFAULT 0,
  `sauv_sauvegarde_compress_command` mediumtext DEFAULT NULL,
  `sauv_sauvegarde_crypt` int(11) DEFAULT 0,
  `sauv_sauvegarde_key1` varchar(32) DEFAULT NULL,
  `sauv_sauvegarde_key2` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`sauv_sauvegarde_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `sauv_sauvegardes`
--

INSERT INTO `sauv_sauvegardes` VALUES
(1, 'tout', 'full', '1', '', '1', 0, 'internal::', 0, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `sauv_tables`
--

CREATE TABLE IF NOT EXISTS `sauv_tables` (
  `sauv_table_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sauv_table_nom` varchar(50) DEFAULT NULL,
  `sauv_table_tables` text DEFAULT NULL,
  PRIMARY KEY (`sauv_table_id`),
  UNIQUE KEY `sauv_table_nom` (`sauv_table_nom`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `sauv_tables`
--

INSERT INTO `sauv_tables` VALUES
(1, 'TOUT', 'abo_liste_lecture,abts_abts,abts_abts_modeles,abts_grille_abt,abts_grille_modele,abts_modeles,abts_periodicites,acces_profiles,acces_rights,actes,admin_session,analysis,arch_emplacement,arch_statut,arch_type,audit,aut_link,author_custom,author_custom_lists,author_custom_values,authorities_sources,authors,authperso,authperso_authorities,authperso_custom,authperso_custom_lists,authperso_custom_values,avis,bannette_abon,bannette_contenu,bannette_equation,bannette_exports,bannette_facettes,bannette_tpl,bannettes,bannettes_descriptors,budgets,bulletins,cache_amendes,caddie,caddie_content,caddie_procs,cashdesk,cashdesk_locations,cashdesk_sections,categ_custom,categ_custom_lists,categ_custom_values,categories,classements,cms,cms_articles,cms_articles_descriptors,cms_build,cms_cache_cadres,cms_cadre_content,cms_cadres,cms_collections,cms_documents,cms_documents_links,cms_editorial_custom,cms_editorial_custom_lists,cms_editorial_custom_values,cms_editorial_fields_global_index,cms_editorial_publications_states,cms_editorial_types,cms_editorial_words_global_index,cms_hash,cms_managed_modules,cms_modules_extensions_datas,cms_pages,cms_pages_env,cms_sections,cms_sections_descriptors,cms_vars,cms_version,collection_custom,collection_custom_lists,collection_custom_values,collections,collections_state,collstate_custom,collstate_custom_lists,collstate_custom_values,comptes,connectors,connectors_categ,connectors_categ_sources,connectors_out,connectors_out_oai_deleted_records,connectors_out_oai_tokens,connectors_out_setcache_values,connectors_out_setcaches,connectors_out_setcateg_sets,connectors_out_setcategs,connectors_out_sets,connectors_out_sources,connectors_out_sources_esgroups,connectors_sources,coordonnees,demandes,demandes_actions,demandes_custom,demandes_custom_lists,demandes_custom_values,demandes_notes,demandes_theme,demandes_type,demandes_users,docs_codestat,docs_location,docs_section,docs_statut,docs_type,docsloc_section,docwatch_categories,docwatch_datasources,docwatch_items,docwatch_items_descriptors,docwatch_items_tags,docwatch_selectors,docwatch_tags,docwatch_watches,dsi_archive,editions_states,empr,empr_caddie,empr_caddie_content,empr_caddie_procs,empr_categ,empr_codestat,empr_custom,empr_custom_lists,empr_custom_values,empr_grilles,empr_groupe,empr_statut,empty_words_calculs,entites,entrepots_localisations,equations,error_log,es_cache,es_cache_blob,es_cache_int,es_converted_cache,es_esgroup_esusers,es_esgroups,es_esusers,es_methods,es_methods_users,es_searchcache,es_searchsessions,etagere,etagere_caddie,exemplaires,exemplaires_temp,exercices,expl_custom,expl_custom_lists,expl_custom_values,explnum,explnum_doc,explnum_doc_actions,explnum_doc_sugg,explnum_location,explnum_segments,explnum_speakers,explnum_statut,external_count,facettes,faq_questions,faq_questions_categories,faq_questions_fields_global_index,faq_questions_words_global_index,faq_themes,faq_types,fiche,frais,gestfic0_custom,gestfic0_custom_lists,gestfic0_custom_values,grilles,groupe,groupexpl,groupexpl_expl,harvest_field,harvest_profil,harvest_profil_import,harvest_profil_import_field,harvest_search_field,harvest_src,import_marc,index_concept,indexint,indexint_custom,indexint_custom_lists,indexint_custom_values,infopages,lenders,liens_actes,lignes_actes,lignes_actes_relances,lignes_actes_statuts,linked_mots,log_expl_retard,log_retard,logopac,mailtpl,map_echelles,map_emprises,map_hold_areas,map_projections,map_refs,mots,noeuds,nomenclature_children_records,nomenclature_exotic_instruments,nomenclature_exotic_other_instruments,nomenclature_families,nomenclature_formations,nomenclature_instruments,nomenclature_musicstands,nomenclature_notices_nomenclatures,nomenclature_types,nomenclature_voices,nomenclature_workshops,nomenclature_workshops_instruments,notice_onglet,notice_statut,notice_tpl,notice_tplcode,notices,notices_authorities_sources,notices_authperso,notices_categories,notices_custom,notices_custom_lists,notices_custom_values,notices_externes,notices_fields_global_index,notices_global_index,notices_langues,notices_mots_global_index,notices_relations,notices_titres_uniformes,offres_remises,onto_uri,opac_filters,opac_liste_lecture,opac_sessions,opac_views,opac_views_empr,origin_authorities,origine_notice,ouvertures,paiements,param_subst,parametres,pclassement,perio_relance,planificateur,pret,pret_archive,pret_custom,pret_custom_lists,pret_custom_values,procs,procs_classements,publisher_custom,publisher_custom_lists,publisher_custom_values,publishers,quotas,quotas_finance,quotas_opac_views,rapport_demandes,rdfstore_g2t,rdfstore_id2val,rdfstore_index,rdfstore_o2val,rdfstore_s2val,rdfstore_setting,rdfstore_triple,recouvrements,resa,resa_archive,resa_loc,resa_planning,resa_ranger,responsability,responsability_tu,rss_content,rss_flux,rss_flux_content,rubriques,sauv_lieux,sauv_log,sauv_sauvegardes,sauv_tables,search_cache,search_perso,search_persopac,search_persopac_empr_categ,serialcirc,serialcirc_ask,serialcirc_circ,serialcirc_copy,serialcirc_diff,serialcirc_expl,serialcirc_group,serialcirc_tpl,serie_custom,serie_custom_lists,serie_custom_values,series,sessions,shorturls,skos_fields_global_index,skos_words_global_index,source_sync,sources_enrichment,statopac,statopac_request,statopac_vues,statopac_vues_col,storages,sub_collections,subcollection_custom,subcollection_custom_lists,subcollection_custom_values,suggestions,suggestions_categ,suggestions_origine,suggestions_source,sur_location,taches,taches_docnum,taches_type,tags,thesaurus,titres_uniformes,transacash,transactions,transactype,transferts,transferts_demande,transferts_source,translation,tris,tu_custom,tu_custom_lists,tu_custom_values,tu_distrib,tu_ref,tu_subdiv,tva_achats,type_abts,type_comptes,types_produits,upload_repertoire,users,users_groups,vedette,vedette_link,vedette_object,visionneuse_params,voir_aussi,words,z_attr,z_bib,z_notices,z_query');

-- --------------------------------------------------------

--
-- Structure de la table `scan_requests`
--

CREATE TABLE IF NOT EXISTS `scan_requests` (
  `id_scan_request` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scan_request_title` varchar(255) NOT NULL DEFAULT '',
  `scan_request_desc` text DEFAULT NULL,
  `scan_request_num_status` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_num_priority` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_create_date` datetime DEFAULT NULL,
  `scan_request_update_date` datetime DEFAULT NULL,
  `scan_request_date` datetime DEFAULT NULL,
  `scan_request_wish_date` datetime DEFAULT NULL,
  `scan_request_deadline_date` datetime DEFAULT NULL,
  `scan_request_comment` text DEFAULT NULL,
  `scan_request_elapsed_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_num_dest_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_num_creator` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_type_creator` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_num_last_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_state` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_as_folder` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_folder_num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_concept_uri` varchar(255) NOT NULL DEFAULT '',
  `scan_request_nb_scanned_pages` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_num_location` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_scan_request`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scan_request_explnum`
--

CREATE TABLE IF NOT EXISTS `scan_request_explnum` (
  `scan_request_explnum_num_request` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_explnum_num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_explnum_num_bulletin` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_explnum_num_explnum` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`scan_request_explnum_num_request`,`scan_request_explnum_num_explnum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scan_request_linked_records`
--

CREATE TABLE IF NOT EXISTS `scan_request_linked_records` (
  `scan_request_linked_record_num_request` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_linked_record_num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_linked_record_num_bulletin` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_linked_record_comment` text DEFAULT NULL,
  `scan_request_linked_record_order` int(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`scan_request_linked_record_num_request`,`scan_request_linked_record_num_notice`,`scan_request_linked_record_num_bulletin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scan_request_priorities`
--

CREATE TABLE IF NOT EXISTS `scan_request_priorities` (
  `id_scan_request_priority` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scan_request_priority_label` varchar(255) NOT NULL DEFAULT '',
  `scan_request_priority_weight` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_scan_request_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scan_request_status`
--

CREATE TABLE IF NOT EXISTS `scan_request_status` (
  `id_scan_request_status` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scan_request_status_label` varchar(255) NOT NULL DEFAULT '',
  `scan_request_status_opac_show` int(1) NOT NULL DEFAULT 0,
  `scan_request_status_cancelable` int(1) NOT NULL DEFAULT 0,
  `scan_request_status_infos_editable` int(1) NOT NULL DEFAULT 0,
  `scan_request_status_class_html` varchar(255) NOT NULL DEFAULT '',
  `scan_request_status_is_closed` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_scan_request_status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `scan_request_status`
--

INSERT INTO `scan_request_status` VALUES
(1, 'Sans statut particulier', 1, 0, 0, '', 0);

-- --------------------------------------------------------

--
-- Structure de la table `scan_request_status_workflow`
--

CREATE TABLE IF NOT EXISTS `scan_request_status_workflow` (
  `scan_request_status_workflow_from_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `scan_request_status_workflow_to_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`scan_request_status_workflow_from_num`,`scan_request_status_workflow_to_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_cache`
--

CREATE TABLE IF NOT EXISTS `search_cache` (
  `object_id` varchar(255) NOT NULL DEFAULT '',
  `delete_on_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `value` mediumblob NOT NULL,
  PRIMARY KEY (`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_engine_ref`
--

CREATE TABLE IF NOT EXISTS `search_engine_ref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(40) NOT NULL,
  `tool_id` varchar(100) NOT NULL,
  `ref_id_high_level` int(11) NOT NULL,
  `ref_id_second_level` int(11) DEFAULT NULL,
  `search_did` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_perso`
--

CREATE TABLE IF NOT EXISTS `search_perso` (
  `search_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `search_type` varchar(255) NOT NULL DEFAULT 'RECORDS',
  `num_user` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `search_name` varchar(255) NOT NULL DEFAULT '',
  `search_shortname` varchar(50) NOT NULL DEFAULT '',
  `search_query` text NOT NULL,
  `search_human` text NOT NULL,
  `search_directlink` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `autorisations` mediumtext DEFAULT NULL,
  `search_order` int(11) NOT NULL DEFAULT 0,
  `search_comment` text NOT NULL,
  PRIMARY KEY (`search_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_persopac`
--

CREATE TABLE IF NOT EXISTS `search_persopac` (
  `search_id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_empr` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `search_name` varchar(255) NOT NULL DEFAULT '',
  `search_shortname` varchar(50) NOT NULL DEFAULT '',
  `search_query` text NOT NULL,
  `search_human` text NOT NULL,
  `search_directlink` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `search_limitsearch` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `search_order` int(11) NOT NULL DEFAULT 0,
  `search_type` varchar(255) NOT NULL DEFAULT 'record',
  PRIMARY KEY (`search_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_persopac_empr_categ`
--

CREATE TABLE IF NOT EXISTS `search_persopac_empr_categ` (
  `id_categ_empr` int(11) NOT NULL DEFAULT 0,
  `id_search_persopac` int(11) NOT NULL DEFAULT 0,
  KEY `i_id_s_persopac` (`id_search_persopac`),
  KEY `i_id_categ_empr` (`id_categ_empr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_segments`
--

CREATE TABLE IF NOT EXISTS `search_segments` (
  `id_search_segment` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `search_segment_label` varchar(255) NOT NULL DEFAULT '',
  `search_segment_description` varchar(255) NOT NULL DEFAULT '',
  `search_segment_template_directory` varchar(255) NOT NULL DEFAULT '',
  `search_segment_num_universe` int(11) NOT NULL DEFAULT 0,
  `search_segment_type` int(11) NOT NULL DEFAULT 0,
  `search_segment_order` int(11) NOT NULL DEFAULT 0,
  `search_segment_set` text DEFAULT NULL,
  `search_segment_logo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_search_segment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_segments_facets`
--

CREATE TABLE IF NOT EXISTS `search_segments_facets` (
  `num_search_segment` int(11) NOT NULL DEFAULT 0,
  `num_facet` int(11) NOT NULL DEFAULT 0,
  `search_segment_facet_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_search_segment`,`num_facet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_segments_search_perso`
--

CREATE TABLE IF NOT EXISTS `search_segments_search_perso` (
  `num_search_segment` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_search_perso` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `search_segment_search_perso_opac` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `search_segment_search_perso_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`num_search_segment`,`num_search_perso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `search_universes`
--

CREATE TABLE IF NOT EXISTS `search_universes` (
  `id_search_universe` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `search_universe_label` varchar(255) NOT NULL DEFAULT '',
  `search_universe_description` varchar(255) NOT NULL DEFAULT '',
  `search_universe_template_directory` varchar(255) NOT NULL DEFAULT '',
  `search_universe_opac_views` varchar(255) NOT NULL DEFAULT '',
  `search_universe_default_segment` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_search_universe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serialcirc`
--

CREATE TABLE IF NOT EXISTS `serialcirc` (
  `id_serialcirc` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_serialcirc_abt` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_virtual` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_duration` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_checked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_retard_mode` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_allow_resa` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_allow_copy` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_allow_send_ask` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_allow_subscription` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_duration_before_send` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_expl_statut_circ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_expl_statut_circ_after` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_state` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_tpl` text NOT NULL,
  `serialcirc_piedpage` text NOT NULL,
  `serialcirc_no_ret` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_sort_diff` text NOT NULL,
  `serialcirc_simple` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_serialcirc`),
  KEY `i_num_serialcirc_abt` (`num_serialcirc_abt`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serialcirc_ask`
--

CREATE TABLE IF NOT EXISTS `serialcirc_ask` (
  `id_serialcirc_ask` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_serialcirc_ask_perio` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_ask_serialcirc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_ask_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_ask_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_ask_statut` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_ask_date` date NOT NULL DEFAULT '0000-00-00',
  `serialcirc_ask_comment` text NOT NULL,
  PRIMARY KEY (`id_serialcirc_ask`),
  KEY `i_num_serialcirc_ask_perio` (`num_serialcirc_ask_perio`),
  KEY `i_num_serialcirc_ask_serialcirc` (`num_serialcirc_ask_serialcirc`),
  KEY `i_num_serialcirc_ask_empr` (`num_serialcirc_ask_empr`),
  KEY `i_serialcirc_ask_type` (`serialcirc_ask_type`),
  KEY `i_serialcirc_ask_statut` (`serialcirc_ask_statut`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serialcirc_circ`
--

CREATE TABLE IF NOT EXISTS `serialcirc_circ` (
  `id_serialcirc_circ` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_serialcirc_circ_diff` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_circ_expl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_circ_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_circ_serialcirc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_circ_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_circ_subscription` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_circ_hold_asked` int(11) NOT NULL DEFAULT 0,
  `serialcirc_circ_ret_asked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_circ_trans_asked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_circ_trans_doc_asked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_circ_expected_date` datetime DEFAULT NULL,
  `serialcirc_circ_pointed_date` datetime DEFAULT NULL,
  `serialcirc_circ_group_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_serialcirc_circ`),
  KEY `i_num_serialcirc_circ_diff` (`num_serialcirc_circ_diff`),
  KEY `i_num_serialcirc_circ_expl` (`num_serialcirc_circ_expl`),
  KEY `i_num_serialcirc_circ_empr` (`num_serialcirc_circ_empr`),
  KEY `i_num_serialcirc_circ_serialcirc` (`num_serialcirc_circ_serialcirc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serialcirc_copy`
--

CREATE TABLE IF NOT EXISTS `serialcirc_copy` (
  `id_serialcirc_copy` int(11) NOT NULL AUTO_INCREMENT,
  `num_serialcirc_copy_empr` int(11) NOT NULL DEFAULT 0,
  `num_serialcirc_copy_bulletin` int(11) NOT NULL DEFAULT 0,
  `serialcirc_copy_analysis` text DEFAULT NULL,
  `serialcirc_copy_date` date NOT NULL DEFAULT '0000-00-00',
  `serialcirc_copy_state` int(11) NOT NULL DEFAULT 0,
  `serialcirc_copy_comment` text NOT NULL,
  PRIMARY KEY (`id_serialcirc_copy`),
  KEY `i_num_serialcirc_copy_empr` (`num_serialcirc_copy_empr`),
  KEY `i_num_serialcirc_copy_bulletin` (`num_serialcirc_copy_bulletin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serialcirc_diff`
--

CREATE TABLE IF NOT EXISTS `serialcirc_diff` (
  `id_serialcirc_diff` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_serialcirc_diff_serialcirc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_diff_empr_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_diff_type_diff` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_diff_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_diff_group_name` varchar(255) NOT NULL DEFAULT '',
  `serialcirc_diff_duration` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_diff_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_serialcirc_diff`),
  KEY `i_num_serialcirc_diff_serialcirc` (`num_serialcirc_diff_serialcirc`),
  KEY `i_serialcirc_diff_empr_type` (`serialcirc_diff_empr_type`),
  KEY `i_serialcirc_diff_type_diff` (`serialcirc_diff_type_diff`),
  KEY `i_num_serialcirc_diff_empr` (`num_serialcirc_diff_empr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serialcirc_expl`
--

CREATE TABLE IF NOT EXISTS `serialcirc_expl` (
  `id_serialcirc_expl` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_serialcirc_expl_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_expl_serialcirc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_expl_bulletine_date` date NOT NULL DEFAULT '0000-00-00',
  `serialcirc_expl_state_circ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_expl_serialcirc_diff` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_expl_ret_asked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_expl_trans_asked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_expl_trans_doc_asked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_expl_current_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_expl_start_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_serialcirc_expl`),
  KEY `i_num_serialcirc_expl_id` (`num_serialcirc_expl_id`),
  KEY `i_num_serialcirc_expl_serialcirc` (`num_serialcirc_expl_serialcirc`),
  KEY `i_num_serialcirc_expl_serialcirc_diff` (`num_serialcirc_expl_serialcirc_diff`),
  KEY `i_num_serialcirc_expl_current_empr` (`num_serialcirc_expl_current_empr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serialcirc_group`
--

CREATE TABLE IF NOT EXISTS `serialcirc_group` (
  `id_serialcirc_group` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_serialcirc_group_diff` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_serialcirc_group_empr` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_group_responsable` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serialcirc_group_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_serialcirc_group`),
  KEY `i_num_serialcirc_group_diff` (`num_serialcirc_group_diff`),
  KEY `i_num_serialcirc_group_empr` (`num_serialcirc_group_empr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serialcirc_tpl`
--

CREATE TABLE IF NOT EXISTS `serialcirc_tpl` (
  `serialcirctpl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `serialcirctpl_name` varchar(255) NOT NULL DEFAULT '',
  `serialcirctpl_comment` varchar(255) NOT NULL DEFAULT '',
  `serialcirctpl_tpl` text NOT NULL,
  `serialcirctpl_piedpage` text NOT NULL,
  PRIMARY KEY (`serialcirctpl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `serialcirc_tpl`
--

INSERT INTO `serialcirc_tpl` VALUES
(1, 'Template PMB', '', 'a:3:{i:0;a:3:{s:4:\"type\";s:4:\"name\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:1;a:3:{s:4:\"type\";s:5:\"ville\";s:2:\"id\";s:1:\"0\";s:5:\"label\";N;}i:2;a:3:{s:4:\"type\";s:5:\"libre\";s:2:\"id\";s:1:\"0\";s:5:\"label\";s:9:\"SIGNATURE\";}}', '');

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE IF NOT EXISTS `series` (
  `serie_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `serie_name` varchar(255) NOT NULL DEFAULT '',
  `serie_index` text DEFAULT NULL,
  PRIMARY KEY (`serie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serie_custom`
--

CREATE TABLE IF NOT EXISTS `serie_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `datatype` varchar(10) NOT NULL DEFAULT '',
  `options` text DEFAULT NULL,
  `multiple` int(11) NOT NULL DEFAULT 0,
  `obligatoire` int(11) NOT NULL DEFAULT 0,
  `ordre` int(11) DEFAULT NULL,
  `search` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `export` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `exclusion_obligatoire` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(11) NOT NULL DEFAULT 100,
  `opac_sort` int(11) NOT NULL DEFAULT 0,
  `comment` blob NOT NULL DEFAULT '',
  `custom_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idchamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serie_custom_dates`
--

CREATE TABLE IF NOT EXISTS `serie_custom_dates` (
  `serie_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serie_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serie_custom_date_type` int(11) DEFAULT NULL,
  `serie_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `serie_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `serie_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`serie_custom_champ`,`serie_custom_origine`,`serie_custom_order`),
  KEY `serie_custom_champ` (`serie_custom_champ`),
  KEY `serie_custom_origine` (`serie_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serie_custom_lists`
--

CREATE TABLE IF NOT EXISTS `serie_custom_lists` (
  `serie_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serie_custom_list_value` varchar(255) DEFAULT NULL,
  `serie_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`serie_custom_champ`),
  KEY `editorial_champ_list_value` (`serie_custom_champ`,`serie_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `serie_custom_values`
--

CREATE TABLE IF NOT EXISTS `serie_custom_values` (
  `serie_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serie_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `serie_custom_small_text` varchar(255) DEFAULT NULL,
  `serie_custom_text` text DEFAULT NULL,
  `serie_custom_integer` int(11) DEFAULT NULL,
  `serie_custom_date` date DEFAULT NULL,
  `serie_custom_float` float DEFAULT NULL,
  `serie_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`serie_custom_champ`),
  KEY `editorial_custom_origine` (`serie_custom_origine`),
  KEY `i_scv_st` (`serie_custom_small_text`),
  KEY `i_scv_t` (`serie_custom_text`(255)),
  KEY `i_scv_i` (`serie_custom_integer`),
  KEY `i_scv_d` (`serie_custom_date`),
  KEY `i_scv_f` (`serie_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_coach` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` char(50) NOT NULL DEFAULT '',
  `nbr_courses` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `nbr_users` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `nbr_classes` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00',
  `nb_days_access_before_beginning` tinyint(4) DEFAULT 0,
  `nb_days_access_after_end` tinyint(4) DEFAULT 0,
  `session_admin_id` int(10) UNSIGNED NOT NULL,
  `visibility` int(11) NOT NULL DEFAULT 1,
  `session_category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `session_admin_id` (`session_admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `SESSID` varchar(12) NOT NULL DEFAULT '',
  `login` varchar(255) NOT NULL DEFAULT '',
  `IP` varchar(20) NOT NULL DEFAULT '',
  `SESSstart` varchar(12) NOT NULL DEFAULT '',
  `LastOn` varchar(12) NOT NULL DEFAULT '',
  `SESSNAME` varchar(25) NOT NULL DEFAULT '',
  `notifications` text DEFAULT NULL,
  PRIMARY KEY (`SESSID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` VALUES
('2624452569', 'admin', '92.92.210.202', '1778155967', '1778156120', 'PhpMyBibli', NULL),
('1462305776', '', '185.132.179.57', '1779142970', '1779142974', 'PmbOpac', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `sessions_tokens`
--

CREATE TABLE IF NOT EXISTS `sessions_tokens` (
  `sessions_tokens_SESSID` varchar(12) NOT NULL DEFAULT '',
  `sessions_tokens_token` varchar(255) NOT NULL DEFAULT '',
  `sessions_tokens_type` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`sessions_tokens_SESSID`,`sessions_tokens_type`),
  KEY `i_st_sessions_tokens_type` (`sessions_tokens_type`),
  KEY `i_st_sessions_tokens_token` (`sessions_tokens_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session_category`
--

CREATE TABLE IF NOT EXISTS `session_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session_field`
--

CREATE TABLE IF NOT EXISTS `session_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_type` int(11) NOT NULL DEFAULT 1,
  `field_variable` varchar(64) NOT NULL,
  `field_display_text` varchar(64) DEFAULT NULL,
  `field_default_value` text DEFAULT NULL,
  `field_order` int(11) DEFAULT NULL,
  `field_visible` tinyint(4) DEFAULT 0,
  `field_changeable` tinyint(4) DEFAULT 0,
  `field_filter` tinyint(4) DEFAULT 0,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session_field_values`
--

CREATE TABLE IF NOT EXISTS `session_field_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` text DEFAULT NULL,
  `tms` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session_rel_course`
--

CREATE TABLE IF NOT EXISTS `session_rel_course` (
  `id_session` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `course_code` char(40) NOT NULL DEFAULT '',
  `nbr_users` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_session`,`course_code`),
  KEY `course_code` (`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session_rel_course_rel_user`
--

CREATE TABLE IF NOT EXISTS `session_rel_course_rel_user` (
  `id_session` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `course_code` char(40) NOT NULL DEFAULT '',
  `id_user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `visibility` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_session`,`course_code`,`id_user`),
  KEY `id_user` (`id_user`),
  KEY `course_code` (`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `session_rel_user`
--

CREATE TABLE IF NOT EXISTS `session_rel_user` (
  `id_session` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `id_user` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_session`,`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `settings_current`
--

CREATE TABLE IF NOT EXISTS `settings_current` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `variable` varchar(255) DEFAULT NULL,
  `subkey` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `selected_value` varchar(255) DEFAULT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `comment` varchar(255) DEFAULT NULL,
  `scope` varchar(50) DEFAULT NULL,
  `subkeytext` varchar(255) DEFAULT NULL,
  `access_url` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `access_url_changeable` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subcategory` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_setting` (`variable`,`subkey`,`category`,`access_url`),
  KEY `access_url` (`access_url`)
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `settings_current`
--

INSERT INTO `settings_current` VALUES
(1, 'Institution', NULL, 'textfield', 'Platform', '', 'InstitutionTitle', 'InstitutionComment', 'platform', NULL, 1, 1, NULL),
(2, 'InstitutionUrl', NULL, 'textfield', 'Platform', '', 'InstitutionUrlTitle', 'InstitutionUrlComment', NULL, NULL, 1, 1, NULL),
(3, 'siteName', NULL, 'textfield', 'Platform', 'TRIADE test', 'SiteNameTitle', 'SiteNameComment', NULL, NULL, 1, 1, NULL),
(4, 'emailAdministrator', NULL, 'textfield', 'Platform', '', 'emailAdministratorTitle', 'emailAdministratorComment', NULL, NULL, 1, 1, NULL),
(5, 'administratorSurname', NULL, 'textfield', 'Platform', 'administrateur', 'administratorSurnameTitle', 'administratorSurnameComment', NULL, NULL, 1, 1, NULL),
(6, 'administratorName', NULL, 'textfield', 'Platform', 'triade', 'administratorNameTitle', 'administratorNameComment', NULL, NULL, 1, 1, NULL),
(7, 'show_administrator_data', NULL, 'radio', 'Platform', 'false', 'ShowAdministratorDataTitle', 'ShowAdministratorDataComment', NULL, NULL, 1, 1, NULL),
(8, 'homepage_view', NULL, 'radio', 'Course', 'activity', 'HomepageViewTitle', 'HomepageViewComment', NULL, NULL, 1, 0, NULL),
(9, 'show_toolshortcuts', NULL, 'radio', 'Course', 'false', 'ShowToolShortcutsTitle', 'ShowToolShortcutsComment', NULL, NULL, 1, 0, NULL),
(10, 'allow_group_categories', NULL, 'radio', 'Course', 'false', 'AllowGroupCategories', 'AllowGroupCategoriesComment', NULL, NULL, 1, 0, NULL),
(11, 'server_type', NULL, 'radio', 'Platform', 'production', 'ServerStatusTitle', 'ServerStatusComment', NULL, NULL, 1, 0, NULL),
(12, 'platformLanguage', NULL, 'link', 'Languages', 'french', 'PlatformLanguageTitle', 'PlatformLanguageComment', NULL, NULL, 1, 0, NULL),
(13, 'showonline', 'world', 'checkbox', 'Platform', 'true', 'ShowOnlineTitle', 'ShowOnlineComment', NULL, 'ShowOnlineWorld', 1, 0, NULL),
(14, 'showonline', 'users', 'checkbox', 'Platform', 'true', 'ShowOnlineTitle', 'ShowOnlineComment', NULL, 'ShowOnlineUsers', 1, 0, NULL),
(15, 'showonline', 'course', 'checkbox', 'Platform', 'true', 'ShowOnlineTitle', 'ShowOnlineComment', NULL, 'ShowOnlineCourse', 1, 0, NULL),
(16, 'profile', 'name', 'checkbox', 'User', 'false', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'name', 1, 0, NULL),
(17, 'profile', 'officialcode', 'checkbox', 'User', 'false', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'officialcode', 1, 0, NULL),
(18, 'profile', 'email', 'checkbox', 'User', 'true', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'Email', 1, 0, NULL),
(19, 'profile', 'picture', 'checkbox', 'User', 'false', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'UserPicture', 1, 0, NULL),
(20, 'profile', 'login', 'checkbox', 'User', 'false', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'Login', 1, 0, NULL),
(21, 'profile', 'password', 'checkbox', 'User', 'false', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'UserPassword', 1, 0, NULL),
(22, 'profile', 'language', 'checkbox', 'User', 'true', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'Language', 1, 0, NULL),
(23, 'default_document_quotum', NULL, 'textfield', 'Course', '500000000', 'DefaultDocumentQuotumTitle', 'DefaultDocumentQuotumComment', NULL, NULL, 1, 0, NULL),
(24, 'registration', 'officialcode', 'checkbox', 'User', 'false', 'RegistrationRequiredFormsTitle', 'RegistrationRequiredFormsComment', NULL, 'OfficialCode', 1, 0, NULL),
(25, 'registration', 'email', 'checkbox', 'User', 'true', 'RegistrationRequiredFormsTitle', 'RegistrationRequiredFormsComment', NULL, 'Email', 1, 0, NULL),
(26, 'registration', 'language', 'checkbox', 'User', 'true', 'RegistrationRequiredFormsTitle', 'RegistrationRequiredFormsComment', NULL, 'Language', 1, 0, NULL),
(27, 'default_group_quotum', NULL, 'textfield', 'Course', '5000000', 'DefaultGroupQuotumTitle', 'DefaultGroupQuotumComment', NULL, NULL, 1, 0, NULL),
(28, 'allow_registration', NULL, 'radio', 'Platform', 'true', 'AllowRegistrationTitle', 'AllowRegistrationComment', NULL, NULL, 1, 0, NULL),
(29, 'allow_registration_as_teacher', NULL, 'radio', 'Platform', 'true', 'AllowRegistrationAsTeacherTitle', 'AllowRegistrationAsTeacherComment', NULL, NULL, 1, 0, NULL),
(30, 'allow_lostpassword', NULL, 'radio', 'Platform', 'false', 'AllowLostPasswordTitle', 'AllowLostPasswordComment', NULL, NULL, 1, 0, NULL),
(31, 'allow_user_headings', NULL, 'radio', 'Course', 'false', 'AllowUserHeadings', 'AllowUserHeadingsComment', NULL, NULL, 1, 0, NULL),
(32, 'course_create_active_tools', 'course_description', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'CourseDescription', 1, 0, NULL),
(33, 'course_create_active_tools', 'agenda', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Agenda', 1, 0, NULL),
(34, 'course_create_active_tools', 'documents', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Documents', 1, 0, NULL),
(35, 'course_create_active_tools', 'learning_path', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'LearningPath', 1, 0, NULL),
(36, 'course_create_active_tools', 'links', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Links', 1, 0, NULL),
(37, 'course_create_active_tools', 'announcements', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Announcements', 1, 0, NULL),
(38, 'course_create_active_tools', 'forums', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Forums', 1, 0, NULL),
(39, 'course_create_active_tools', 'dropbox', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Dropbox', 1, 0, NULL),
(40, 'course_create_active_tools', 'quiz', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Quiz', 1, 0, NULL),
(41, 'course_create_active_tools', 'users', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Users', 1, 0, NULL),
(42, 'course_create_active_tools', 'groups', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Groups', 1, 0, NULL),
(43, 'course_create_active_tools', 'chat', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Chat', 1, 0, NULL),
(44, 'course_create_active_tools', 'online_conference', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'OnlineConference', 1, 0, NULL),
(45, 'course_create_active_tools', 'student_publications', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'StudentPublications', 1, 0, NULL),
(46, 'allow_personal_agenda', NULL, 'radio', 'User', 'false', 'AllowPersonalAgendaTitle', 'AllowPersonalAgendaComment', NULL, NULL, 1, 0, NULL),
(47, 'display_coursecode_in_courselist', NULL, 'radio', 'Platform', 'true', 'DisplayCourseCodeInCourselistTitle', 'DisplayCourseCodeInCourselistComment', NULL, NULL, 1, 0, NULL),
(48, 'display_teacher_in_courselist', NULL, 'radio', 'Platform', 'true', 'DisplayTeacherInCourselistTitle', 'DisplayTeacherInCourselistComment', NULL, NULL, 1, 0, NULL),
(49, 'use_document_title', NULL, 'radio', 'Tools', 'false', 'UseDocumentTitleTitle', 'UseDocumentTitleComment', NULL, NULL, 1, 0, NULL),
(50, 'permanently_remove_deleted_files', NULL, 'radio', 'Tools', 'false', 'PermanentlyRemoveFilesTitle', 'PermanentlyRemoveFilesComment', NULL, NULL, 1, 0, NULL),
(51, 'dropbox_allow_overwrite', NULL, 'radio', 'Tools', 'true', 'DropboxAllowOverwriteTitle', 'DropboxAllowOverwriteComment', NULL, NULL, 1, 0, NULL),
(52, 'dropbox_max_filesize', NULL, 'textfield', 'Tools', '100000000', 'DropboxMaxFilesizeTitle', 'DropboxMaxFilesizeComment', NULL, NULL, 1, 0, NULL),
(53, 'dropbox_allow_just_upload', NULL, 'radio', 'Tools', 'true', 'DropboxAllowJustUploadTitle', 'DropboxAllowJustUploadComment', NULL, NULL, 1, 0, NULL),
(54, 'dropbox_allow_student_to_student', NULL, 'radio', 'Tools', 'false', 'DropboxAllowStudentToStudentTitle', 'DropboxAllowStudentToStudentComment', NULL, NULL, 1, 0, NULL),
(55, 'dropbox_allow_group', NULL, 'radio', 'Tools', 'true', 'DropboxAllowGroupTitle', 'DropboxAllowGroupComment', NULL, NULL, 1, 0, NULL),
(56, 'dropbox_allow_mailing', NULL, 'radio', 'Tools', 'false', 'DropboxAllowMailingTitle', 'DropboxAllowMailingComment', NULL, NULL, 1, 0, NULL),
(57, 'administratorTelephone', NULL, 'textfield', 'Platform', '', 'administratorTelephoneTitle', 'administratorTelephoneComment', NULL, NULL, 1, 1, NULL),
(58, 'extended_profile', NULL, 'radio', 'User', 'true', 'ExtendedProfileTitle', 'ExtendedProfileComment', NULL, NULL, 1, 0, NULL),
(59, 'student_view_enabled', NULL, 'radio', 'Platform', 'true', 'StudentViewEnabledTitle', 'StudentViewEnabledComment', NULL, NULL, 1, 0, NULL),
(60, 'show_navigation_menu', NULL, 'radio', 'Course', 'false', 'ShowNavigationMenuTitle', 'ShowNavigationMenuComment', NULL, NULL, 1, 0, NULL),
(61, 'enable_tool_introduction', NULL, 'radio', 'course', 'false', 'EnableToolIntroductionTitle', 'EnableToolIntroductionComment', NULL, NULL, 1, 0, NULL),
(62, 'page_after_login', NULL, 'radio', 'Platform', 'user_portal.php', 'PageAfterLoginTitle', 'PageAfterLoginComment', NULL, NULL, 1, 0, NULL),
(63, 'time_limit_whosonline', NULL, 'textfield', 'Platform', '30', 'TimeLimitWhosonlineTitle', 'TimeLimitWhosonlineComment', NULL, NULL, 1, 0, NULL),
(64, 'breadcrumbs_course_homepage', NULL, 'radio', 'Course', 'session_name_and_course_title', 'BreadCrumbsCourseHomepageTitle', 'BreadCrumbsCourseHomepageComment', NULL, NULL, 1, 0, NULL),
(65, 'example_material_course_creation', NULL, 'radio', 'Platform', 'false', 'ExampleMaterialCourseCreationTitle', 'ExampleMaterialCourseCreationComment', NULL, NULL, 1, 0, NULL),
(66, 'account_valid_duration', NULL, 'textfield', 'Platform', '365', 'AccountValidDurationTitle', 'AccountValidDurationComment', NULL, NULL, 1, 0, NULL),
(67, 'use_session_mode', NULL, 'radio', 'Platform', 'true', 'UseSessionModeTitle', 'UseSessionModeComment', NULL, NULL, 1, 0, NULL),
(68, 'allow_email_editor', NULL, 'radio', 'Tools', 'false', 'AllowEmailEditorTitle', 'AllowEmailEditorComment', NULL, NULL, 1, 0, NULL),
(69, 'registered', NULL, 'textfield', NULL, 'false', '', NULL, NULL, NULL, 1, 0, NULL),
(70, 'donotlistcampus', NULL, 'textfield', NULL, 'false', '', NULL, NULL, NULL, 1, 0, NULL),
(71, 'show_email_addresses', NULL, 'radio', 'Platform', 'false', 'ShowEmailAddresses', 'ShowEmailAddressesComment', NULL, NULL, 1, 1, NULL),
(72, 'profile', 'phone', 'checkbox', 'User', 'false', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'phone', 1, 0, NULL),
(77, 'service_ppt2lp', 'active', 'radio', NULL, 'false', 'ppt2lp_actived', '', NULL, NULL, 1, 0, NULL),
(78, 'service_ppt2lp', 'host', 'textfield', NULL, NULL, 'Host', NULL, NULL, NULL, 1, 0, NULL),
(79, 'service_ppt2lp', 'port', 'textfield', NULL, '2002', 'Port', NULL, NULL, NULL, 1, 0, NULL),
(80, 'service_ppt2lp', 'user', 'textfield', NULL, NULL, 'UserOnHost', NULL, NULL, NULL, 1, 0, NULL),
(81, 'service_ppt2lp', 'ftp_password', 'textfield', NULL, NULL, 'FtpPassword', NULL, NULL, NULL, 1, 0, NULL),
(82, 'service_ppt2lp', 'path_to_lzx', 'textfield', NULL, NULL, '', NULL, NULL, NULL, 1, 0, NULL),
(83, 'service_ppt2lp', 'size', 'radio', NULL, '720x540', '', NULL, NULL, NULL, 1, 0, NULL),
(84, 'wcag_anysurfer_public_pages', NULL, 'radio', 'Platform', 'false', 'PublicPagesComplyToWAITitle', 'PublicPagesComplyToWAIComment', NULL, NULL, 1, 0, NULL),
(85, 'stylesheets', NULL, 'textfield', 'stylesheets', 'dokeos2_orange', '', NULL, NULL, NULL, 1, 1, NULL),
(86, 'upload_extensions_list_type', NULL, 'radio', 'Security', 'whitelist', 'UploadExtensionsListType', 'UploadExtensionsListTypeComment', NULL, NULL, 1, 0, NULL),
(87, 'upload_extensions_blacklist', NULL, 'textfield', 'Security', 'php;php5;php3;php4;exe;com;bat;scr;js;', 'UploadExtensionsBlacklist', 'UploadExtensionsBlacklistComment', NULL, NULL, 1, 0, NULL),
(88, 'upload_extensions_whitelist', NULL, 'textfield', 'Security', 'htm;html;jpg;jpeg;gif;png;swf;avi;mpg;mpeg;odt;docx;xlsx;ppt;txt;odg;ods;odf;odp;xcf', 'UploadExtensionsWhitelist', 'UploadExtensionsWhitelistComment', NULL, NULL, 1, 0, NULL),
(89, 'upload_extensions_skip', NULL, 'radio', 'Security', 'true', 'UploadExtensionsSkip', 'UploadExtensionsSkipComment', NULL, NULL, 1, 0, NULL),
(90, 'upload_extensions_replace_by', NULL, 'textfield', 'Security', 'dangerous', 'UploadExtensionsReplaceBy', 'UploadExtensionsReplaceByComment', NULL, NULL, 1, 0, NULL),
(91, 'show_number_of_courses', NULL, 'radio', 'Platform', 'false', 'ShowNumberOfCourses', 'ShowNumberOfCoursesComment', NULL, NULL, 1, 0, NULL),
(92, 'show_empty_course_categories', NULL, 'radio', 'Platform', 'true', 'ShowEmptyCourseCategories', 'ShowEmptyCourseCategoriesComment', NULL, NULL, 1, 0, NULL),
(93, 'show_back_link_on_top_of_tree', NULL, 'radio', 'Platform', 'false', 'ShowBackLinkOnTopOfCourseTree', 'ShowBackLinkOnTopOfCourseTreeComment', NULL, NULL, 1, 0, NULL),
(94, 'show_different_course_language', NULL, 'radio', 'Platform', 'true', 'ShowDifferentCourseLanguage', 'ShowDifferentCourseLanguageComment', NULL, NULL, 1, 1, NULL),
(95, 'split_users_upload_directory', NULL, 'radio', 'Tuning', 'true', 'SplitUsersUploadDirectory', 'SplitUsersUploadDirectoryComment', NULL, NULL, 1, 0, NULL),
(96, 'hide_dltt_markup', NULL, 'radio', 'Platform', 'true', 'HideDLTTMarkup', 'HideDLTTMarkupComment', NULL, NULL, 1, 0, NULL),
(97, 'display_categories_on_homepage', NULL, 'radio', 'Platform', 'false', 'DisplayCategoriesOnHomepageTitle', 'DisplayCategoriesOnHomepageComment', NULL, NULL, 1, 1, NULL),
(98, 'permissions_for_new_directories', NULL, 'textfield', 'Security', '0777', 'PermissionsForNewDirs', 'PermissionsForNewDirsComment', NULL, NULL, 1, 0, NULL),
(99, 'permissions_for_new_files', NULL, 'textfield', 'Security', '0666', 'PermissionsForNewFiles', 'PermissionsForNewFilesComment', NULL, NULL, 1, 0, NULL),
(100, 'show_tabs', 'campus_homepage', 'checkbox', 'Platform', 'true', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsCampusHomepage', 1, 1, NULL),
(101, 'show_tabs', 'my_courses', 'checkbox', 'Platform', 'true', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsMyCourses', 1, 1, NULL),
(102, 'show_tabs', 'reporting', 'checkbox', 'Platform', 'true', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsReporting', 1, 1, NULL),
(103, 'show_tabs', 'platform_administration', 'checkbox', 'Platform', 'true', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsPlatformAdministration', 1, 1, NULL),
(104, 'show_tabs', 'my_agenda', 'checkbox', 'Platform', 'true', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsMyAgenda', 1, 1, NULL),
(105, 'show_tabs', 'my_profile', 'checkbox', 'Platform', 'false', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsMyProfile', 1, 1, NULL),
(106, 'default_forum_view', NULL, 'radio', 'Course', 'flat', 'DefaultForumViewTitle', 'DefaultForumViewComment', NULL, NULL, 1, 0, NULL),
(107, 'platform_charset', NULL, 'textfield', 'Platform', 'iso-8859-15', 'PlatformCharsetTitle', 'PlatformCharsetComment', 'platform', NULL, 1, 0, NULL),
(108, 'noreply_email_address', '', 'textfield', 'Platform', '', 'NoReplyEmailAddress', 'NoReplyEmailAddressComment', NULL, NULL, 1, 0, NULL),
(109, 'survey_email_sender_noreply', '', 'radio', 'Course', 'noreply', 'SurveyEmailSenderNoReply', 'SurveyEmailSenderNoReplyComment', NULL, NULL, 1, 0, NULL),
(110, 'openid_authentication', NULL, 'radio', 'Security', 'false', 'OpenIdAuthentication', 'OpenIdAuthenticationComment', NULL, NULL, 1, 0, NULL),
(111, 'profile', 'openid', 'checkbox', 'User', 'false', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'OpenIDURL', 1, 0, NULL),
(112, 'gradebook_enable', NULL, 'radio', 'Gradebook', 'false', 'GradebookActivation', 'GradebookActivationComment', NULL, NULL, 1, 0, NULL),
(113, 'show_tabs', 'my_gradebook', 'checkbox', 'Platform', 'true', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsMyGradebook', 1, 1, NULL),
(114, 'gradebook_score_display_coloring', 'my_display_coloring', 'checkbox', 'Gradebook', 'false', 'GradebookScoreDisplayColoring', 'GradebookScoreDisplayColoringComment', NULL, 'TabsGradebookEnableColoring', 1, 0, NULL),
(115, 'gradebook_score_display_custom', 'my_display_custom', 'checkbox', 'Gradebook', 'false', 'GradebookScoreDisplayCustom', 'GradebookScoreDisplayCustomComment', NULL, 'TabsGradebookEnableCustom', 1, 0, NULL),
(116, 'gradebook_score_display_colorsplit', NULL, 'textfield', 'Gradebook', '50', 'GradebookScoreDisplayColorSplit', 'GradebookScoreDisplayColorSplitComment', NULL, NULL, 1, 0, NULL),
(117, 'gradebook_score_display_upperlimit', 'my_display_upperlimit', 'checkbox', 'Gradebook', 'false', 'GradebookScoreDisplayUpperLimit', 'GradebookScoreDisplayUpperLimitComment', NULL, 'TabsGradebookEnableUpperLimit', 1, 0, NULL),
(118, 'user_selected_theme', NULL, 'radio', 'Platform', 'false', 'UserThemeSelection', 'UserThemeSelectionComment', NULL, NULL, 1, 0, NULL),
(119, 'profile', 'theme', 'checkbox', 'User', 'true', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'UserTheme', 1, 0, NULL),
(120, 'allow_course_theme', NULL, 'radio', 'Course', 'true', 'AllowCourseThemeTitle', 'AllowCourseThemeComment', NULL, NULL, 1, 0, NULL),
(121, 'display_mini_month_calendar', NULL, 'radio', 'Tools', 'true', 'DisplayMiniMonthCalendarTitle', 'DisplayMiniMonthCalendarComment', NULL, NULL, 1, 0, NULL),
(122, 'display_upcoming_events', NULL, 'radio', 'Tools', 'true', 'DisplayUpcomingEventsTitle', 'DisplayUpcomingEventsComment', NULL, NULL, 1, 0, NULL),
(123, 'number_of_upcoming_events', NULL, 'textfield', 'Tools', '1', 'NumberOfUpcomingEventsTitle', 'NumberOfUpcomingEventsComment', NULL, NULL, 1, 0, NULL),
(124, 'show_closed_courses', NULL, 'radio', 'Platform', 'false', 'ShowClosedCoursesTitle', 'ShowClosedCoursesComment', NULL, NULL, 1, 0, NULL),
(125, 'ldap_main_server_address', NULL, 'textfield', 'LDAP', 'localhost', 'LDAPMainServerAddressTitle', 'LDAPMainServerAddressComment', NULL, NULL, 1, 0, NULL),
(126, 'ldap_main_server_port', NULL, 'textfield', 'LDAP', '389', 'LDAPMainServerPortTitle', 'LDAPMainServerPortComment', NULL, NULL, 1, 0, NULL),
(127, 'ldap_domain', NULL, 'textfield', 'LDAP', 'dc=nodomain', 'LDAPDomainTitle', 'LDAPDomainComment', NULL, NULL, 1, 0, NULL),
(128, 'ldap_replicate_server_address', NULL, 'textfield', 'LDAP', 'localhost', 'LDAPReplicateServerAddressTitle', 'LDAPReplicateServerAddressComment', NULL, NULL, 1, 0, NULL),
(129, 'ldap_replicate_server_port', NULL, 'textfield', 'LDAP', '389', 'LDAPReplicateServerPortTitle', 'LDAPReplicateServerPortComment', NULL, NULL, 1, 0, NULL),
(130, 'ldap_search_term', NULL, 'textfield', 'LDAP', '', 'LDAPSearchTermTitle', 'LDAPSearchTermComment', NULL, NULL, 1, 0, NULL),
(131, 'ldap_version', NULL, 'radio', 'LDAP', '3', 'LDAPVersionTitle', 'LDAPVersionComment', NULL, '', 1, 0, NULL),
(132, 'ldap_filled_tutor_field', NULL, 'textfield', 'LDAP', 'employeenumber', 'LDAPFilledTutorFieldTitle', 'LDAPFilledTutorFieldComment', NULL, '', 1, 0, NULL),
(133, 'ldap_authentication_login', NULL, 'textfield', 'LDAP', '', 'LDAPAuthenticationLoginTitle', 'LDAPAuthenticationLoginComment', NULL, '', 1, 0, NULL),
(134, 'ldap_authentication_password', NULL, 'textfield', 'LDAP', '', 'LDAPAuthenticationPasswordTitle', 'LDAPAuthenticationPasswordComment', NULL, '', 1, 0, NULL),
(136, 'extendedprofile_registration', 'mycomptetences', 'checkbox', 'User', 'true', 'ExtendedProfileRegistrationTitle', 'ExtendedProfileRegistrationComment', NULL, 'MyCompetences', 1, 0, NULL),
(137, 'extendedprofile_registration', 'mydiplomas', 'checkbox', 'User', 'true', 'ExtendedProfileRegistrationTitle', 'ExtendedProfileRegistrationComment', NULL, 'MyDiplomas', 1, 0, NULL),
(138, 'extendedprofile_registration', 'myteach', 'checkbox', 'User', 'true', 'ExtendedProfileRegistrationTitle', 'ExtendedProfileRegistrationComment', NULL, 'MyTeach', 1, 0, NULL),
(139, 'extendedprofile_registration', 'mypersonalopenarea', 'checkbox', 'User', 'false', 'ExtendedProfileRegistrationTitle', 'ExtendedProfileRegistrationComment', NULL, 'MyPersonalOpenArea', 1, 0, NULL),
(140, 'extendedprofile_registrationrequired', 'mycomptetences', 'checkbox', 'User', 'false', 'ExtendedProfileRegistrationRequiredTitle', 'ExtendedProfileRegistrationRequiredComment', NULL, 'MyCompetences', 1, 0, NULL),
(141, 'extendedprofile_registrationrequired', 'mydiplomas', 'checkbox', 'User', 'false', 'ExtendedProfileRegistrationRequiredTitle', 'ExtendedProfileRegistrationRequiredComment', NULL, 'MyDiplomas', 1, 0, NULL),
(142, 'extendedprofile_registrationrequired', 'myteach', 'checkbox', 'User', 'false', 'ExtendedProfileRegistrationRequiredTitle', 'ExtendedProfileRegistrationRequiredComment', NULL, 'MyTeach', 1, 0, NULL),
(143, 'extendedprofile_registrationrequired', 'mypersonalopenarea', 'checkbox', 'User', 'false', 'ExtendedProfileRegistrationRequiredTitle', 'ExtendedProfileRegistrationRequiredComment', NULL, 'MyPersonalOpenArea', 1, 0, NULL),
(144, 'ldap_filled_tutor_field_value', NULL, 'textfield', 'LDAP', '', 'LDAPFilledTutorFieldValueTitle', 'LDAPFilledTutorFieldValueComment', NULL, '', 1, 0, NULL),
(145, 'service_visio', 'active', 'radio', NULL, 'false', 'VisioEnable', '', NULL, NULL, 1, 0, NULL),
(146, 'service_visio', 'visio_host', 'textfield', NULL, '', 'VisioHost', '', NULL, NULL, 1, 0, NULL),
(147, 'service_visio', 'visio_port', 'textfield', NULL, '1935', 'VisioPort', '', NULL, NULL, 1, 0, NULL),
(148, 'service_visio', 'visio_pass', 'textfield', NULL, '', 'VisioPassword', '', NULL, NULL, 1, 0, NULL),
(149, 'service_visio', 'visio_use_rtmpt', 'radio', NULL, 'false', 'VisioUseRtmptTitle', 'VisioUseRtmptComment', NULL, NULL, 1, 0, NULL),
(150, 'registration', 'phone', 'textfield', 'User', 'false', 'RegistrationRequiredFormsTitle', 'RegistrationRequiredFormsComment', NULL, 'Phone', 1, 0, NULL),
(151, 'add_users_by_coach', NULL, 'radio', 'Security', 'false', 'AddUsersByCoachTitle', 'AddUsersByCoachComment', NULL, NULL, 1, 0, NULL),
(152, 'course_create_active_tools', 'wiki', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Wiki', 1, 0, NULL),
(153, 'extend_rights_for_coach', NULL, 'radio', 'Security', 'false', 'ExtendRightsForCoachTitle', 'ExtendRightsForCoachComment', NULL, NULL, 1, 0, NULL),
(154, 'extend_rights_for_coach_on_surveys', NULL, 'radio', 'Security', 'false', 'ExtendRightsForCoachOnSurveyTitle', 'ExtendRightsForCoachOnSurveyComment', NULL, NULL, 1, 0, NULL),
(155, 'show_session_coach', NULL, 'radio', 'Platform', 'false', 'ShowSessionCoachTitle', 'ShowSessionCoachComment', NULL, NULL, 1, 0, NULL),
(156, 'course_create_active_tools', 'gradebook', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Gradebook', 1, 0, NULL),
(157, 'course_create_active_tools', 'glossary', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Glossary', 1, 0, NULL),
(158, 'course_create_active_tools', 'notebook', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Notebook', 1, 0, NULL),
(159, 'allow_users_to_create_courses', NULL, 'radio', 'Course', 'true', 'AllowUsersToCreateCoursesTitle', 'AllowUsersToCreateCoursesComment', NULL, NULL, 1, 0, NULL),
(160, 'course_create_active_tools', 'survey', 'checkbox', 'Tools', 'true', 'CourseCreateActiveToolsTitle', 'CourseCreateActiveToolsComment', NULL, 'Survey', 1, 0, NULL),
(161, 'allow_reservation', NULL, 'radio', 'Tools', 'false', 'AllowReservationTitle', 'AllowReservationComment', NULL, NULL, 1, 0, NULL),
(162, 'advanced_filemanager', NULL, 'radio', 'Platform', 'false', 'AdvancedFileManagerTitle', 'AdvancedFileManagerComment', NULL, NULL, 1, 0, NULL),
(163, 'allow_message_tool', NULL, 'radio', 'Tools', 'true', 'AllowMessageToolTitle', 'AllowMessageToolComment', NULL, NULL, 1, 0, NULL),
(164, 'allow_social_tool', NULL, 'radio', 'Tools', 'true', 'AllowSocialToolTitle', 'AllowSocialToolComment', NULL, NULL, 1, 0, NULL),
(165, 'allow_students_to_browse_courses', NULL, 'radio', 'Platform', 'true', 'AllowStudentsToBrowseCoursesTitle', 'AllowStudentsToBrowseCoursesComment', NULL, NULL, 1, 1, NULL),
(166, 'profile', 'apikeys', 'checkbox', 'User', 'false', 'ProfileChangesTitle', 'ProfileChangesComment', NULL, 'ApiKeys', 1, 0, NULL),
(167, 'dokeos_database_version', NULL, 'textfield', NULL, '2.0.11278', 'DokeosDatabaseVersion', '', NULL, NULL, 1, 0, NULL),
(168, 'allow_use_sub_language', NULL, 'radio', 'Platform', 'false', 'AllowUseSubLanguageTitle', 'AllowUseSubLanguageComment', NULL, NULL, 1, 0, NULL),
(169, 'show_glossary_in_documents', NULL, 'radio', 'Course', 'isautomatic', 'ShowGlossaryInDocumentsTitle', 'ShowGlossaryInDocumentsComment', NULL, NULL, 1, 1, NULL),
(170, 'allow_terms_conditions', NULL, 'radio', 'Platform', 'false', 'AllowTermsAndConditionsTitle', 'AllowTermsAndConditionsComment', NULL, NULL, 1, 0, NULL),
(171, 'show_tutor_data', NULL, 'radio', 'Platform', 'true', 'ShowTutorDataTitle', 'ShowTutorDataComment', NULL, NULL, 1, 1, NULL),
(172, 'show_teacher_data', NULL, 'radio', 'Platform', 'true', 'ShowTeacherDataTitle', 'ShowTeacherDataComment', NULL, NULL, 1, 1, NULL),
(173, 'search_enabled', NULL, 'radio', 'Tools', 'false', 'EnableSearchTitle', 'EnableSearchComment', NULL, NULL, 1, 0, NULL),
(174, 'search_prefilter_prefix', NULL, NULL, 'Search', '', 'SearchPrefilterPrefix', 'SearchPrefilterPrefixComment', NULL, NULL, 1, 0, NULL),
(175, 'search_show_unlinked_results', NULL, 'radio', 'Search', 'true', 'SearchShowUnlinkedResultsTitle', 'SearchShowUnlinkedResultsComment', NULL, NULL, 1, 0, NULL),
(176, 'dokeos_database_version', NULL, 'textfield', NULL, '2.0.11278', 'DokeosDatabaseVersion', '', NULL, NULL, 1, 0, NULL),
(177, 'allow_coach_to_edit_course_session', NULL, 'radio', 'Course', 'false', 'AllowCoachsToEditInsideTrainingSessions', 'AllowCoachsToEditInsideTrainingSessionsComment', NULL, NULL, 1, 0, NULL),
(178, 'show_courses_descriptions_in_catalog', NULL, 'radio', 'Course', 'true', 'ShowCoursesDescriptionsInCatalogTitle', 'ShowCoursesDescriptionsInCatalogComment', NULL, NULL, 1, 1, NULL),
(179, 'show_glossary_in_extra_tools', NULL, 'radio', 'Course', 'false', 'ShowGlossaryInExtraToolsTitle', 'ShowGlossaryInExtraToolsComment', NULL, NULL, 1, 0, NULL),
(180, 'send_email_to_admin_when_create_course', NULL, 'radio', 'Platform', 'false', 'SendEmailToAdminTitle', 'SendEmailToAdminComment', NULL, NULL, 1, 1, NULL),
(181, 'go_to_course_after_login', NULL, 'radio', 'Course', 'false', 'GoToCourseAfterLoginTitle', 'GoToCourseAfterLoginComment', NULL, NULL, 1, 0, NULL),
(182, 'user_order_by', NULL, 'radio', 'User', 'lastname', 'OrderUsersByTitle', 'OrderUsersByComment', NULL, NULL, 1, 1, NULL),
(183, 'read_more_limit', NULL, 'textfield', 'Tools', '100', 'ReadMoreLimitTitle', 'ReadMoreLimitComment', NULL, NULL, 1, 1, NULL),
(184, 'installation_date', NULL, 'textfield', 'System', '1396190402', 'InstallationDateTitle', 'InstallationDateComment', NULL, NULL, 1, 0, NULL),
(185, 'widget_homepage', NULL, 'radio', 'Course', 'widgethomepage2', 'WidgetHomepageTitle', 'WidgetHomepageComment', '0', NULL, 1, 0, NULL),
(186, 'widget_hidden_title_behaviour', NULL, 'radio', 'Course', 'showonhover', 'WidgetHiddenTitleBehaviourTitle', 'WidgetHiddenTitleBehaviourComment', '0', NULL, 1, 0, NULL),
(187, 'portal_view', NULL, 'radio', 'Platform', 'classic', 'PortalViewTitle', 'PortalViewComment', NULL, NULL, 1, 0, NULL),
(188, 'cas_activate', NULL, 'radio', 'CAS', 'false', 'CasMainActivateTitle', 'CasMainActivateComment', NULL, NULL, 1, 0, NULL),
(189, 'cas_server', NULL, 'textfield', 'CAS', '', 'CasMainServerTitle', 'CasMainServerComment', NULL, NULL, 1, 0, NULL),
(190, 'cas_server_uri', NULL, 'textfield', 'CAS', '', 'CasMainServerURITitle', 'CasMainServerURIComment', NULL, NULL, 1, 0, NULL),
(191, 'cas_port', NULL, 'textfield', 'CAS', '', 'CasMainPortTitle', 'CasMainPortComment', NULL, NULL, 1, 0, NULL),
(192, 'cas_protocol', NULL, 'radio', 'CAS', '', 'CasMainProtocolTitle', 'CasMainProtocolComment', NULL, NULL, 1, 0, NULL),
(193, 'cas_add_user_activate', NULL, 'radio', 'CAS', '', 'CasUserAddActivateTitle', 'CasUserAddActivateComment', NULL, NULL, 1, 0, NULL),
(194, 'cas_add_user_login_attr', NULL, 'textfield', 'CAS', '', 'CasUserAddLoginAttributeTitle', 'CasUserAddLoginAttributeComment', NULL, NULL, 1, 0, NULL),
(195, 'cas_add_user_email_attr', NULL, 'textfield', 'CAS', '', 'CasUserAddEmailAttributeTitle', 'CasUserAddEmailAttributeComment', NULL, NULL, 1, 0, NULL),
(196, 'cas_add_user_firstname_attr', NULL, 'textfield', 'CAS', '', 'CasUserAddFirstnameAttributeTitle', 'CasUserAddFirstnameAttributeComment', NULL, NULL, 1, 0, NULL),
(197, 'cas_add_user_lastname_attr', NULL, 'textfield', 'CAS', '', 'CasUserAddLastnameAttributeTitle', 'CasUserAddLastnameAttributeComment', NULL, NULL, 1, 0, NULL),
(198, 'calendar_types', 'platformevents', 'checkbox', 'Tools', 'true', 'CalendarTypesTitle', 'CalendarTypesComment', '1', NULL, 1, 1, 'calendar'),
(199, 'calendar_types', 'quizevents', 'checkbox', 'Tools', 'true', 'CalendarTypesTitle', 'CalendarTypesComment', '1', NULL, 1, 1, 'calendar'),
(200, 'calendar_types', 'sessionevents', 'checkbox', 'Tools', 'true', 'CalendarTypesTitle', 'CalendarTypesComment', '1', NULL, 1, 1, 'calendar'),
(201, 'mindmap_converter_activated', '', 'radio', 'Platform', 'false', 'MindmapConverterTitle', 'MindmapConverterComment', NULL, NULL, 1, 0, NULL),
(202, 'agenda_default_view', '', 'radio', 'Tools', 'agendaWeek', 'AgendaDefaultViewTitle', 'AgendaDefaultViewComment', '1', NULL, 1, 1, 'calendar'),
(203, 'agenda_action_icons', '', 'radio', 'Tools', 'false', 'AgendaActionIconsTitle', 'AgendaActionIconsComment', '1', NULL, 1, 1, 'calendar'),
(204, 'calendar_detail_view', '', 'radio', 'Tools', 'false', 'CalendarDetailViewTitle', 'CalendarDetailViewComment', '1', NULL, 1, 1, 'calendar'),
(205, 'calendar_navigation', '', 'radio', 'Tools', 'actions', 'CalendarNavigationTitle', 'CalendarNavigationComment', '1', NULL, 1, 1, 'calendar'),
(206, 'display_feedback_messages', '', 'radio', 'Platform', 'false', 'DisplayFeedbackMessagesTitle', 'DisplayFeedbackMessagesComment', '1', NULL, 1, 1, ''),
(207, 'agenda_show_actions_on_edit', '', 'radio', 'Tools', 'true', 'AgendaShowActionsOnEditTitle', 'AgendaShowActionsOnEditComment', '1', NULL, 1, 1, 'calendar'),
(208, 'user_manage_group_agenda', '', 'radio', 'Tools', 'true', 'CanUsersMangeGroupAgendaTitle', 'CanUsersMangeGroupAgendaComment', '1', NULL, 1, 1, 'calendar'),
(209, 'captcha', '', 'radio', 'Platform', 'false', 'CaptchaTitle', 'CaptchaComment', NULL, NULL, 1, 1, ''),
(210, 'number_of_announcements', '', 'textfield', 'Tools', '8', 'NumberOfAnnouncementsInListTitle', 'NumberOfAnnouncementsInListComment', '0', NULL, 1, 1, 'announcements'),
(211, 'calendar_export_all', '', 'radio', 'Tools', 'false', 'CalendarExportAllTitle', 'CalendarExportAllComment', '0', NULL, 1, 1, 'calendar'),
(212, 'display_context_help', '', 'radio', 'Platform', 'false', 'DisplayContextHelpTitle', 'DisplayContextHelpComment', '0', NULL, 1, 1, 'help'),
(213, 'display_breadcrumbs', '', 'radio', 'Platform', 'false', 'DisplayBreadcrumbsTitle', 'DisplayBreadcrumbsComment', '0', NULL, 1, 1, 'navigation'),
(214, 'display_platform_header_in_course', '', 'radio', 'Platform', 'hide', 'DisplayPlatformHeaderInCourseTitle', 'DisplayPlatformHeaderInCourseComment', NULL, NULL, 1, 1, 'navigation'),
(215, 'groupscenariofield', 'description', 'checkbox', 'Tools', 'false', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldDescription', 1, 0, NULL),
(216, 'groupscenariofield', 'limit', 'checkbox', 'Tools', 'true', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldLimit', 1, 0, NULL),
(217, 'groupscenariofield', 'registration', 'checkbox', 'Tools', 'true', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldRegistration', 1, 0, NULL),
(218, 'groupscenariofield', 'unregistration', 'checkbox', 'Tools', 'false', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldUnRegistration', 1, 0, NULL),
(219, 'groupscenariofield', 'publicprivategroup', 'checkbox', 'Tools', 'true', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldPublicPrivateGroup', 1, 0, NULL),
(220, 'groupscenariofield', 'document', 'checkbox', 'Tools', 'false', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldDocument', 1, 0, NULL),
(221, 'groupscenariofield', 'work', 'checkbox', 'Tools', 'false', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldWork', 1, 0, NULL),
(222, 'groupscenariofield', 'calendar', 'checkbox', 'Tools', 'false', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldCalendar', 1, 0, NULL),
(223, 'groupscenariofield', 'announcements', 'checkbox', 'Tools', 'false', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldAnnouncements', 1, 0, NULL),
(224, 'groupscenariofield', 'forum', 'checkbox', 'Tools', 'false', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldForum', 1, 0, NULL),
(225, 'groupscenariofield', 'wiki', 'checkbox', 'Tools', 'false', 'GroupScenarioFieldTitle', 'GroupScenarioFieldComment', '0', 'GroupScenarioFieldWiki', 1, 0, NULL),
(226, 'show_tabs', 'social', 'checkbox', 'Platform', 'true', 'ShowTabsTitle', 'ShowTabsComment', NULL, 'TabsSocial', 1, 0, NULL),
(227, 'force_password_change', '', 'textfield', 'Security', 'false', 'ForcePasswordChangeTitle', 'ForcePasswordChangeComment', NULL, NULL, 1, 1, ''),
(228, 'force_password_change_account_creation', '', 'radio', 'Security', 'false', 'ForcePasswordChangeAccountCreationTitle', 'ForcePasswordChangeAccountCreationComment', NULL, NULL, 1, 1, ''),
(229, 'password_rule', 'numbers', 'checkbox', 'Security', 'true', 'PasswordRuleTitle', 'PasswordRuleComment', NULL, 'PasswordRuleNumbers', 1, 1, ''),
(230, 'password_rule', 'camelcase', 'checkbox', 'Security', 'true', 'PasswordRuleTitle', 'PasswordRuleComment', NULL, 'PasswordRuleCamelCase', 1, 1, ''),
(231, 'password_rule', 'symbols', 'checkbox', 'Security', 'false', 'PasswordRuleTitle', 'PasswordRuleComment', NULL, 'PasswordRuleSymbol', 1, 1, ''),
(232, 'password_length', '', 'textfield', 'Security', '6', 'PasswordLengthTitle', 'PasswordLengthComment', NULL, NULL, 1, 1, ''),
(233, 'login_fail_lock', '', 'textfield', 'Security', '0', 'LoginFailLockTitle', 'LoginFailLockComment', NULL, NULL, 1, 1, ''),
(234, 'allow_user_edit_agenda', '', 'radio', 'Tools', 'false', 'AllowUserEditAgendaTitle', 'AllowUserEditAgendaTitle', '1', NULL, 1, 1, NULL),
(235, 'block_copy_paste_for_students', NULL, 'radio', 'Editor', 'false', 'BlockCopyPasteForStudentsTitle', 'BlockCopyPasteForStudentsComment', NULL, NULL, 1, 0, NULL),
(236, 'extend_rights_for_coach_on_survey', NULL, 'radio', 'Security', 'true', 'ExtendRightsForCoachOnSurveyTitle', 'ExtendRightsForCoachOnSurveyComment', NULL, NULL, 1, 0, NULL),
(237, 'math_asciimathML', NULL, 'radio', 'Editor', 'false', 'MathASCIImathMLTitle', 'MathASCIImathMLComment', NULL, NULL, 1, 0, NULL),
(238, 'math_mimetex', NULL, 'radio', 'Editor', 'false', 'MathMimetexTitle', 'MathMimetexComment', NULL, NULL, 1, 0, NULL),
(239, 'message_max_upload_filesize', NULL, 'textfield', 'Tools', '20971520', 'MessageMaxUploadFilesizeTitle', 'MessageMaxUploadFilesizeComment', NULL, NULL, 1, 0, NULL),
(240, 'more_buttons_maximized_mode', NULL, 'radio', 'Editor', 'false', 'MoreButtonsForMaximizedModeTitle', 'MoreButtonsForMaximizedModeComment', NULL, NULL, 1, 0, NULL),
(241, 'show_quizcategory', '', 'radio', 'Platform', 'false', 'ShowQuizCategoryTitle', 'ShowQuizCategoryComment', '0', NULL, 1, 1, NULL),
(242, 'show_session_data', NULL, 'radio', 'Course', 'false', 'ShowSessionDataTitle', 'ShowSessionDataComment', NULL, NULL, 1, 1, NULL),
(243, 'students_download_folders', NULL, 'radio', 'Tools', 'true', 'AllowStudentsDownloadFoldersTitle', 'AllowStudentsDownloadFoldersComment', NULL, NULL, 1, 0, NULL),
(244, 'youtube_for_students', NULL, 'radio', 'Editor', 'true', 'YoutubeForStudentsTitle', 'YoutubeForStudentsComment', NULL, NULL, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `settings_options`
--

CREATE TABLE IF NOT EXISTS `settings_options` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `variable` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `display_text` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=228 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `settings_options`
--

INSERT INTO `settings_options` VALUES
(1, 'show_administrator_data', 'true', 'Yes'),
(2, 'show_administrator_data', 'false', 'No'),
(3, 'homepage_view', 'activity', 'HomepageViewActivity'),
(4, 'homepage_view', '2column', 'HomepageView2column'),
(5, 'homepage_view', '3column', 'HomepageView3column'),
(6, 'show_toolshortcuts', 'true', 'Yes'),
(7, 'show_toolshortcuts', 'false', 'No'),
(8, 'allow_group_categories', 'true', 'Yes'),
(9, 'allow_group_categories', 'false', 'No'),
(10, 'server_type', 'production', 'ProductionServer'),
(11, 'server_type', 'test', 'TestServer'),
(12, 'allow_name_change', 'true', 'Yes'),
(13, 'allow_name_change', 'false', 'No'),
(14, 'allow_officialcode_change', 'false', 'No'),
(15, 'allow_officialcode_change', 'false', 'No'),
(16, 'allow_registration', 'true', 'Yes'),
(17, 'allow_registration', 'false', 'No'),
(18, 'allow_registration', 'approval', 'AfterApproval'),
(19, 'allow_registration_as_teacher', 'true', 'Yes'),
(20, 'allow_registration_as_teacher', 'false', 'No'),
(21, 'allow_lostpassword', 'true', 'Yes'),
(22, 'allow_lostpassword', 'false', 'No'),
(23, 'allow_user_headings', 'true', 'Yes'),
(24, 'allow_user_headings', 'false', 'No'),
(25, 'allow_personal_agenda', 'true', 'Yes'),
(26, 'allow_personal_agenda', 'false', 'No'),
(27, 'display_coursecode_in_courselist', 'true', 'Yes'),
(28, 'display_coursecode_in_courselist', 'false', 'No'),
(29, 'display_teacher_in_courselist', 'true', 'Yes'),
(30, 'display_teacher_in_courselist', 'false', 'No'),
(31, 'use_document_title', 'true', 'Yes'),
(32, 'use_document_title', 'false', 'No'),
(33, 'permanently_remove_deleted_files', 'true', 'Yes'),
(34, 'permanently_remove_deleted_files', 'false', 'No'),
(35, 'dropbox_allow_overwrite', 'true', 'Yes'),
(36, 'dropbox_allow_overwrite', 'false', 'No'),
(37, 'dropbox_allow_just_upload', 'true', 'Yes'),
(38, 'dropbox_allow_just_upload', 'false', 'No'),
(39, 'dropbox_allow_student_to_student', 'true', 'Yes'),
(40, 'dropbox_allow_student_to_student', 'false', 'No'),
(41, 'dropbox_allow_group', 'true', 'Yes'),
(42, 'dropbox_allow_group', 'false', 'No'),
(43, 'dropbox_allow_mailing', 'true', 'Yes'),
(44, 'dropbox_allow_mailing', 'false', 'No'),
(45, 'extended_profile', 'true', 'Yes'),
(46, 'extended_profile', 'false', 'No'),
(47, 'student_view_enabled', 'true', 'Yes'),
(48, 'student_view_enabled', 'false', 'No'),
(49, 'show_navigation_menu', 'false', 'No'),
(50, 'show_navigation_menu', 'icons', 'IconsOnly'),
(51, 'show_navigation_menu', 'text', 'TextOnly'),
(52, 'show_navigation_menu', 'iconstext', 'IconsText'),
(53, 'enable_tool_introduction', 'true', 'Yes'),
(54, 'enable_tool_introduction', 'false', 'No'),
(55, 'page_after_login', 'index.php', 'CampusHomepage'),
(56, 'page_after_login', 'user_portal.php', 'MyCourses'),
(57, 'breadcrumbs_course_homepage', 'get_lang', 'CourseHomepage'),
(58, 'breadcrumbs_course_homepage', 'course_code', 'CourseCode'),
(59, 'breadcrumbs_course_homepage', 'course_title', 'CourseTitle'),
(60, 'example_material_course_creation', 'true', 'Yes'),
(61, 'example_material_course_creation', 'false', 'No'),
(62, 'use_session_mode', 'true', 'Yes'),
(63, 'use_session_mode', 'false', 'No'),
(64, 'allow_email_editor', 'true', 'Yes'),
(65, 'allow_email_editor', 'false', 'No'),
(66, 'show_email_addresses', 'true', 'Yes'),
(67, 'show_email_addresses', 'false', 'No'),
(68, 'wcag_anysurfer_public_pages', 'true', 'Yes'),
(69, 'wcag_anysurfer_public_pages', 'false', 'No'),
(70, 'upload_extensions_list_type', 'blacklist', 'Blacklist'),
(71, 'upload_extensions_list_type', 'whitelist', 'Whitelist'),
(72, 'upload_extensions_skip', 'true', 'Remove'),
(73, 'upload_extensions_skip', 'false', 'Rename'),
(74, 'show_number_of_courses', 'true', 'Yes'),
(75, 'show_number_of_courses', 'false', 'No'),
(76, 'show_empty_course_categories', 'true', 'Yes'),
(77, 'show_empty_course_categories', 'false', 'No'),
(78, 'show_back_link_on_top_of_tree', 'true', 'Yes'),
(79, 'show_back_link_on_top_of_tree', 'false', 'No'),
(80, 'show_different_course_language', 'true', 'Yes'),
(81, 'show_different_course_language', 'false', 'No'),
(82, 'split_users_upload_directory', 'true', 'Yes'),
(83, 'split_users_upload_directory', 'false', 'No'),
(84, 'hide_dltt_markup', 'false', 'No'),
(85, 'hide_dltt_markup', 'true', 'Yes'),
(86, 'display_categories_on_homepage', 'true', 'Yes'),
(87, 'display_categories_on_homepage', 'false', 'No'),
(88, 'default_forum_view', 'flat', 'Flat'),
(89, 'default_forum_view', 'threaded', 'Threaded'),
(90, 'default_forum_view', 'nested', 'Nested'),
(91, 'survey_email_sender_noreply', 'coach', 'CourseCoachEmailSender'),
(92, 'survey_email_sender_noreply', 'noreply', 'NoReplyEmailSender'),
(93, 'openid_authentication', 'true', 'Yes'),
(94, 'openid_authentication', 'false', 'No'),
(95, 'gradebook_enable', 'true', 'Yes'),
(96, 'gradebook_enable', 'false', 'No'),
(97, 'user_selected_theme', 'true', 'Yes'),
(98, 'user_selected_theme', 'false', 'No'),
(99, 'allow_course_theme', 'true', 'Yes'),
(100, 'allow_course_theme', 'false', 'No'),
(101, 'display_mini_month_calendar', 'true', 'Yes'),
(102, 'display_mini_month_calendar', 'false', 'No'),
(103, 'display_upcoming_events', 'true', 'Yes'),
(104, 'display_upcoming_events', 'false', 'No'),
(105, 'show_closed_courses', 'true', 'Yes'),
(106, 'show_closed_courses', 'false', 'No'),
(107, 'ldap_version', '2', 'LDAPVersion2'),
(108, 'ldap_version', '3', 'LDAPVersion3'),
(109, 'visio_use_rtmpt', 'true', 'Yes'),
(110, 'visio_use_rtmpt', 'false', 'No'),
(111, 'add_users_by_coach', 'true', 'Yes'),
(112, 'add_users_by_coach', 'false', 'No'),
(113, 'extend_rights_for_coach', 'true', 'Yes'),
(114, 'extend_rights_for_coach', 'false', 'No'),
(115, 'extend_rights_for_coach_on_surveys', 'true', 'Yes'),
(116, 'extend_rights_for_coach_on_surveys', 'false', 'No'),
(117, 'show_session_coach', 'true', 'Yes'),
(118, 'show_session_coach', 'false', 'No'),
(119, 'allow_users_to_create_courses', 'true', 'Yes'),
(120, 'allow_users_to_create_courses', 'false', 'No'),
(121, 'breadcrumbs_course_homepage', 'session_name_and_course_title', 'SessionNameAndCourseTitle'),
(122, 'allow_reservation', 'true', 'Yes'),
(123, 'allow_reservation', 'false', 'No'),
(124, 'advanced_filemanager', 'true', 'Yes'),
(125, 'advanced_filemanager', 'false', 'No'),
(126, 'allow_message_tool', 'true', 'Yes'),
(127, 'allow_message_tool', 'false', 'No'),
(128, 'allow_social_tool', 'true', 'Yes'),
(129, 'allow_social_tool', 'false', 'No'),
(130, 'allow_students_to_browse_courses', 'true', 'Yes'),
(131, 'allow_students_to_browse_courses', 'false', 'No'),
(132, 'allow_use_sub_language', 'true', 'Yes'),
(133, 'allow_use_sub_language', 'false', 'No'),
(134, 'show_glossary_in_documents', 'none', 'ShowGlossaryInDocumentsIsNone'),
(135, 'show_glossary_in_documents', 'ismanual', 'ShowGlossaryInDocumentsIsManual'),
(136, 'show_glossary_in_documents', 'isautomatic', 'ShowGlossaryInDocumentsIsAutomatic'),
(137, 'allow_terms_conditions', 'true', 'Yes'),
(138, 'allow_terms_conditions', 'false', 'No'),
(139, 'show_tutor_data', 'true', 'Yes'),
(140, 'show_tutor_data', 'false', 'No'),
(141, 'show_teacher_data', 'true', 'Yes'),
(142, 'show_teacher_data', 'false', 'No'),
(143, 'search_enabled', 'true', 'Yes'),
(144, 'search_enabled', 'false', 'No'),
(145, 'search_show_unlinked_results', 'true', 'SearchShowUnlinkedResults'),
(146, 'search_show_unlinked_results', 'false', 'SearchHideUnlinkedResults'),
(147, 'show_courses_descriptions_in_catalog', 'true', 'Yes'),
(148, 'show_courses_descriptions_in_catalog', 'false', 'No'),
(149, 'allow_coach_to_edit_course_session', 'true', 'Yes'),
(150, 'allow_coach_to_edit_course_session', 'false', 'No'),
(151, 'show_glossary_in_extra_tools', 'true', 'Yes'),
(152, 'show_glossary_in_extra_tools', 'false', 'No'),
(153, 'send_email_to_admin_when_create_course', 'true', 'Yes'),
(154, 'send_email_to_admin_when_create_course', 'false', 'No'),
(155, 'go_to_course_after_login', 'true', 'Yes'),
(156, 'go_to_course_after_login', 'false', 'No'),
(157, 'user_order_by', 'firstname', 'FirstName'),
(158, 'user_order_by', 'lastname', 'LastName'),
(159, 'cas_activate', 'true', 'Yes'),
(160, 'cas_activate', 'false', 'No'),
(161, 'cas_protocol', 'CAS1', 'CAS1Text'),
(162, 'cas_protocol', 'CAS2', 'CAS2Text'),
(163, 'cas_protocol', 'SAML', 'SAMLText'),
(164, 'cas_add_user_activate', 'true', 'Yes'),
(165, 'cas_add_user_activate', 'false', 'No'),
(166, 'mindmap_converter_activated', 'true', 'Yes'),
(167, 'mindmap_converter_activated', 'false', 'No'),
(168, 'agenda_default_view', 'month', 'MonthView'),
(169, 'agenda_default_view', 'agendaWeek', 'WeekView'),
(170, 'agenda_default_view', 'agendaDay', 'DayView'),
(171, 'agenda_action_icons', 'true', 'Yes'),
(172, 'agenda_action_icons', 'false', 'No'),
(173, 'calendar_detail_view', 'edit', 'EditView'),
(174, 'calendar_detail_view', 'detail', 'DetailView'),
(175, 'calendar_navigation', 'actions', 'CalendarNavigationActions'),
(176, 'calendar_navigation', 'default', 'CalendarNavigationDefault'),
(177, 'display_feedback_messages', 'true', 'Yes'),
(178, 'display_feedback_messages', 'false', 'No'),
(179, 'agenda_show_actions_on_edit', 'true', 'Yes'),
(180, 'agenda_show_actions_on_edit', 'false', 'No'),
(181, 'user_manage_group_agenda', 'true', 'Yes'),
(182, 'user_manage_group_agenda', 'false', 'No'),
(183, 'captcha', 'true', 'Yes'),
(184, 'captcha', 'false', 'No'),
(185, 'calendar_export_all', 'true', 'Yes'),
(186, 'calendar_export_all', 'false', 'No'),
(187, 'display_context_help', 'true', 'Yes'),
(188, 'display_context_help', 'false', 'No'),
(189, 'display_breadcrumbs', 'true', 'Yes'),
(190, 'display_breadcrumbs', 'false', 'No'),
(191, 'display_platform_header_in_course', 'show', 'ShowPlatformHeaderInCourse'),
(192, 'display_platform_header_in_course', 'hide', 'HidePlatformHeaderInCourse'),
(193, 'display_platform_header_in_course', 'toggle', 'TogglePlatformHeaderInCourse'),
(194, 'force_password_change_account_creation', 'true', 'Yes'),
(195, 'force_password_change_account_creation', 'false', 'No'),
(196, 'allow_user_edit_agenda', 'true', 'Yes'),
(197, 'allow_user_edit_agenda', 'false', 'No'),
(198, 'block_copy_paste_for_students', 'true', 'Yes'),
(199, 'block_copy_paste_for_students', 'false', 'No'),
(200, 'extend_rights_for_coach_on_survey', 'true', 'Yes'),
(201, 'extend_rights_for_coach_on_survey', 'false', 'No'),
(202, 'math_asciimathML', 'true', 'Yes'),
(203, 'math_asciimathML', 'false', 'No'),
(204, 'math_mimetex', 'true', 'Yes'),
(205, 'math_mimetex', 'false', 'No'),
(206, 'more_buttons_maximized_mode', 'true', 'Yes'),
(207, 'more_buttons_maximized_mode', 'false', 'No'),
(208, 'show_quizcategory', 'true', 'Yes'),
(209, 'show_quizcategory', 'false', 'No'),
(210, 'show_session_data ', 'true', 'Yes'),
(211, 'show_session_data ', 'false', 'No'),
(212, 'students_download_folders', 'true', 'Yes'),
(213, 'students_download_folders', 'false', 'No'),
(214, 'youtube_for_students', 'true', 'Yes'),
(215, 'youtube_for_students', 'false', 'No'),
(216, 'portal_view', 'widget', 'PortalViewWidget'),
(217, 'portal_view', 'classic', 'PortalViewClassic'),
(218, 'show_email_of_teacher_or_tutor ', 'true', 'Yes'),
(219, 'show_email_of_teacher_or_tutor ', 'false', 'No'),
(220, 'widget_hidden_title_behaviour', 'showonhover', 'ShowOnHover'),
(221, 'widget_hidden_title_behaviour', 'showtoggle', 'ShowToggle'),
(222, 'widget_homepage', 'widgethomepage1', 'widgethomepage1'),
(223, 'widget_homepage', 'widgethomepage2', 'widgethomepage2'),
(224, 'widget_homepage', 'widgethomepage3', 'widgethomepage3'),
(225, 'widget_homepage', 'widgethomepage4', 'widgethomepage4'),
(226, 'widget_homepage', 'widgethomepage5', 'widgethomepage5'),
(227, 'widget_homepage', 'widgethomepage6', 'widgethomepage6');

-- --------------------------------------------------------

--
-- Structure de la table `shared_survey`
--

CREATE TABLE IF NOT EXISTS `shared_survey` (
  `survey_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(20) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `subtitle` text DEFAULT NULL,
  `author` varchar(250) DEFAULT NULL,
  `lang` varchar(20) DEFAULT NULL,
  `template` varchar(20) DEFAULT NULL,
  `intro` text DEFAULT NULL,
  `surveythanks` text DEFAULT NULL,
  `creation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `course_code` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`survey_id`),
  UNIQUE KEY `id` (`survey_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `shared_survey_question`
--

CREATE TABLE IF NOT EXISTS `shared_survey_question` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL DEFAULT 0,
  `survey_question` text NOT NULL,
  `survey_question_comment` text NOT NULL,
  `type` varchar(250) NOT NULL DEFAULT '',
  `display` varchar(10) NOT NULL DEFAULT '',
  `sort` int(11) NOT NULL DEFAULT 0,
  `code` varchar(40) NOT NULL DEFAULT '',
  `max_value` int(11) NOT NULL,
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `shared_survey_question_option`
--

CREATE TABLE IF NOT EXISTS `shared_survey_question_option` (
  `question_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL DEFAULT 0,
  `survey_id` int(11) NOT NULL DEFAULT 0,
  `option_text` text NOT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`question_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `shorturls`
--

CREATE TABLE IF NOT EXISTS `shorturls` (
  `id_shorturl` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `shorturl_hash` varchar(255) NOT NULL DEFAULT '',
  `shorturl_last_access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `shorturl_context` text NOT NULL,
  `shorturl_type` varchar(255) NOT NULL DEFAULT '',
  `shorturl_action` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_shorturl`),
  KEY `i_shorturl_hash` (`shorturl_hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `single_sign_on_association`
--

CREATE TABLE IF NOT EXISTS `single_sign_on_association` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` text NOT NULL,
  `date_end` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `login_status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `skos_custom`
--

CREATE TABLE IF NOT EXISTS `skos_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `datatype` varchar(10) NOT NULL DEFAULT '',
  `options` text DEFAULT NULL,
  `multiple` int(11) NOT NULL DEFAULT 0,
  `obligatoire` int(11) NOT NULL DEFAULT 0,
  `ordre` int(11) DEFAULT NULL,
  `search` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `export` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `exclusion_obligatoire` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(11) NOT NULL DEFAULT 100,
  `opac_sort` int(11) NOT NULL DEFAULT 0,
  `comment` blob NOT NULL DEFAULT '',
  `custom_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idchamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `skos_custom_dates`
--

CREATE TABLE IF NOT EXISTS `skos_custom_dates` (
  `skos_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `skos_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `skos_custom_date_type` int(11) DEFAULT NULL,
  `skos_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `skos_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `skos_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`skos_custom_champ`,`skos_custom_origine`,`skos_custom_order`),
  KEY `skos_custom_champ` (`skos_custom_champ`),
  KEY `skos_custom_origine` (`skos_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `skos_custom_lists`
--

CREATE TABLE IF NOT EXISTS `skos_custom_lists` (
  `skos_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `skos_custom_list_value` varchar(255) DEFAULT NULL,
  `skos_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `skos_custom_champ` (`skos_custom_champ`),
  KEY `skos_champ_list_value` (`skos_custom_champ`,`skos_custom_list_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `skos_custom_values`
--

CREATE TABLE IF NOT EXISTS `skos_custom_values` (
  `skos_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `skos_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `skos_custom_small_text` varchar(255) DEFAULT NULL,
  `skos_custom_text` text DEFAULT NULL,
  `skos_custom_integer` int(11) DEFAULT NULL,
  `skos_custom_date` date DEFAULT NULL,
  `skos_custom_float` float DEFAULT NULL,
  `skos_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `skos_custom_champ` (`skos_custom_champ`),
  KEY `i_encv_st` (`skos_custom_small_text`),
  KEY `i_encv_t` (`skos_custom_text`(255)),
  KEY `i_encv_i` (`skos_custom_integer`),
  KEY `i_encv_d` (`skos_custom_date`),
  KEY `i_encv_f` (`skos_custom_float`),
  KEY `skos_custom_origine` (`skos_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `skos_fields_global_index`
--

CREATE TABLE IF NOT EXISTS `skos_fields_global_index` (
  `id_item` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code_champ` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `code_ss_champ` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `ordre` int(4) UNSIGNED NOT NULL DEFAULT 0,
  `value` text NOT NULL,
  `pond` int(4) UNSIGNED NOT NULL DEFAULT 100,
  `lang` varchar(10) NOT NULL DEFAULT '',
  `authority_num` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_item`,`code_champ`,`code_ss_champ`,`lang`,`ordre`),
  KEY `i_value` (`value`(300)),
  KEY `i_code_champ_code_ss_champ` (`code_champ`,`code_ss_champ`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `skos_words_global_index`
--

CREATE TABLE IF NOT EXISTS `skos_words_global_index` (
  `id_item` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `code_ss_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_word` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(10) UNSIGNED NOT NULL DEFAULT 100,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `field_position` int(10) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_item`,`code_champ`,`num_word`,`position`,`code_ss_champ`),
  KEY `code_champ` (`code_champ`),
  KEY `i_id_mot` (`num_word`,`id_item`),
  KEY `i_code_champ_code_ss_champ_num_word` (`code_champ`,`code_ss_champ`,`num_word`),
  KEY `i_num_word` (`num_word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sources_enrichment`
--

CREATE TABLE IF NOT EXISTS `sources_enrichment` (
  `source_enrichment_num` int(11) NOT NULL DEFAULT 0,
  `source_enrichment_typnotice` varchar(2) NOT NULL DEFAULT '',
  `source_enrichment_typdoc` varchar(2) NOT NULL DEFAULT '',
  `source_enrichment_params` text NOT NULL,
  PRIMARY KEY (`source_enrichment_num`,`source_enrichment_typnotice`,`source_enrichment_typdoc`),
  KEY `i_s_enrichment_typnoti` (`source_enrichment_typnotice`),
  KEY `i_s_enrichment_typdoc` (`source_enrichment_typdoc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `source_sync`
--

CREATE TABLE IF NOT EXISTS `source_sync` (
  `source_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `nrecu` varchar(255) NOT NULL DEFAULT '',
  `ntotal` varchar(255) NOT NULL DEFAULT '',
  `message` varchar(255) NOT NULL DEFAULT '',
  `date_sync` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `percent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `env` text NOT NULL,
  `cancel` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`source_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `specific_field`
--

CREATE TABLE IF NOT EXISTS `specific_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(1) NOT NULL,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_specific_field__code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `specific_field_values`
--

CREATE TABLE IF NOT EXISTS `specific_field_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(40) NOT NULL,
  `tool_id` varchar(100) NOT NULL,
  `ref_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `statopac`
--

CREATE TABLE IF NOT EXISTS `statopac` (
  `id_log` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_log` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `url_demandee` varchar(255) NOT NULL DEFAULT '',
  `url_referente` varchar(255) NOT NULL DEFAULT '',
  `get_log` blob NOT NULL,
  `post_log` blob NOT NULL,
  `num_session` varchar(255) NOT NULL DEFAULT '0',
  `server_log` blob NOT NULL,
  `empr_carac` blob NOT NULL,
  `empr_doc` blob NOT NULL,
  `empr_expl` mediumblob NOT NULL,
  `nb_result` blob NOT NULL,
  `gen_stat` blob NOT NULL,
  PRIMARY KEY (`id_log`),
  KEY `sopac_date_log` (`date_log`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `statopac_request`
--

CREATE TABLE IF NOT EXISTS `statopac_request` (
  `idproc` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `requete` blob NOT NULL,
  `comment` tinytext NOT NULL,
  `parameters` text NOT NULL,
  `num_vue` mediumint(8) NOT NULL DEFAULT 0,
  `autorisations` mediumtext NOT NULL,
  PRIMARY KEY (`idproc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `statopac_vues`
--

CREATE TABLE IF NOT EXISTS `statopac_vues` (
  `id_vue` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_consolidation` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `nom_vue` varchar(255) NOT NULL DEFAULT '',
  `comment` tinytext NOT NULL,
  `date_debut_log` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_fin_log` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id_vue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `statopac_vues_col`
--

CREATE TABLE IF NOT EXISTS `statopac_vues_col` (
  `id_col` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_col` varchar(255) NOT NULL DEFAULT '',
  `expression` varchar(255) NOT NULL DEFAULT '',
  `num_vue` mediumint(8) NOT NULL DEFAULT 0,
  `ordre` mediumint(8) NOT NULL DEFAULT 0,
  `filtre` varchar(255) NOT NULL DEFAULT '',
  `datatype` varchar(10) NOT NULL DEFAULT '',
  `maj_flag` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_col`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sticks_sheets`
--

CREATE TABLE IF NOT EXISTS `sticks_sheets` (
  `id_sticks_sheet` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sticks_sheet_label` varchar(255) NOT NULL DEFAULT '',
  `sticks_sheet_data` text NOT NULL,
  `sticks_sheet_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_sticks_sheet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `storages`
--

CREATE TABLE IF NOT EXISTS `storages` (
  `id_storage` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `storage_name` varchar(255) NOT NULL DEFAULT '',
  `storage_class` varchar(255) NOT NULL DEFAULT '',
  `storage_params` text NOT NULL,
  PRIMARY KEY (`id_storage`),
  KEY `i_storage_class` (`storage_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subcollection_custom`
--

CREATE TABLE IF NOT EXISTS `subcollection_custom` (
  `idchamp` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `datatype` varchar(10) NOT NULL DEFAULT '',
  `options` text DEFAULT NULL,
  `multiple` int(11) NOT NULL DEFAULT 0,
  `obligatoire` int(11) NOT NULL DEFAULT 0,
  `ordre` int(11) DEFAULT NULL,
  `search` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `export` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `exclusion_obligatoire` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `pond` int(11) NOT NULL DEFAULT 100,
  `opac_sort` int(11) NOT NULL DEFAULT 0,
  `comment` blob NOT NULL DEFAULT '',
  `custom_classement` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`idchamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subcollection_custom_dates`
--

CREATE TABLE IF NOT EXISTS `subcollection_custom_dates` (
  `subcollection_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subcollection_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subcollection_custom_date_type` int(11) DEFAULT NULL,
  `subcollection_custom_date_start` int(11) NOT NULL DEFAULT 0,
  `subcollection_custom_date_end` int(11) NOT NULL DEFAULT 0,
  `subcollection_custom_order` int(11) NOT NULL,
  PRIMARY KEY (`subcollection_custom_champ`,`subcollection_custom_origine`,`subcollection_custom_order`),
  KEY `subcollection_custom_champ` (`subcollection_custom_champ`),
  KEY `subcollection_custom_origine` (`subcollection_custom_origine`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subcollection_custom_lists`
--

CREATE TABLE IF NOT EXISTS `subcollection_custom_lists` (
  `subcollection_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subcollection_custom_list_value` varchar(255) DEFAULT NULL,
  `subcollection_custom_list_lib` varchar(255) DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  KEY `editorial_custom_champ` (`subcollection_custom_champ`),
  KEY `editorial_champ_list_value` (`subcollection_custom_champ`,`subcollection_custom_list_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subcollection_custom_values`
--

CREATE TABLE IF NOT EXISTS `subcollection_custom_values` (
  `subcollection_custom_champ` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subcollection_custom_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subcollection_custom_small_text` varchar(255) DEFAULT NULL,
  `subcollection_custom_text` text DEFAULT NULL,
  `subcollection_custom_integer` int(11) DEFAULT NULL,
  `subcollection_custom_date` date DEFAULT NULL,
  `subcollection_custom_float` float DEFAULT NULL,
  `subcollection_custom_order` int(11) NOT NULL DEFAULT 0,
  KEY `editorial_custom_champ` (`subcollection_custom_champ`),
  KEY `editorial_custom_origine` (`subcollection_custom_origine`),
  KEY `i_scv_st` (`subcollection_custom_small_text`),
  KEY `i_scv_t` (`subcollection_custom_text`(255)),
  KEY `i_scv_i` (`subcollection_custom_integer`),
  KEY `i_scv_d` (`subcollection_custom_date`),
  KEY `i_scv_f` (`subcollection_custom_float`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subst_files`
--

CREATE TABLE IF NOT EXISTS `subst_files` (
  `id_subst_file` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subst_file_path` varchar(255) NOT NULL DEFAULT '',
  `subst_file_filename` varchar(255) NOT NULL DEFAULT '',
  `subst_file_data` mediumtext NOT NULL,
  PRIMARY KEY (`id_subst_file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sub_collections`
--

CREATE TABLE IF NOT EXISTS `sub_collections` (
  `sub_coll_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_coll_name` varchar(255) NOT NULL DEFAULT '',
  `sub_coll_parent` mediumint(9) UNSIGNED NOT NULL DEFAULT 0,
  `sub_coll_issn` varchar(12) NOT NULL DEFAULT '',
  `index_sub_coll` text DEFAULT NULL,
  `subcollection_web` text NOT NULL,
  `subcollection_comment` text NOT NULL,
  `authority_import_denied` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`sub_coll_id`),
  KEY `sub_coll_name` (`sub_coll_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suggestions`
--

CREATE TABLE IF NOT EXISTS `suggestions` (
  `id_suggestion` int(12) UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` tinytext NOT NULL,
  `editeur` varchar(255) NOT NULL DEFAULT '',
  `auteur` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL DEFAULT '',
  `prix` float(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `commentaires` text DEFAULT NULL,
  `commentaires_gestion` text DEFAULT NULL,
  `statut` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `num_produit` int(8) NOT NULL DEFAULT 0,
  `num_entite` int(5) NOT NULL DEFAULT 0,
  `index_suggestion` text NOT NULL,
  `nb` int(5) UNSIGNED NOT NULL DEFAULT 1,
  `date_creation` date NOT NULL DEFAULT '0000-00-00',
  `date_decision` date NOT NULL DEFAULT '0000-00-00',
  `num_rubrique` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `num_fournisseur` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_notice` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `url_suggestion` varchar(255) NOT NULL DEFAULT '',
  `num_categ` int(12) NOT NULL DEFAULT 1,
  `sugg_location` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `sugg_source` int(8) NOT NULL DEFAULT 0,
  `date_publication` varchar(255) NOT NULL DEFAULT '',
  `notice_unimarc` blob NOT NULL,
  PRIMARY KEY (`id_suggestion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suggestions_categ`
--

CREATE TABLE IF NOT EXISTS `suggestions_categ` (
  `id_categ` int(12) NOT NULL AUTO_INCREMENT,
  `libelle_categ` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_categ`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `suggestions_categ`
--

INSERT INTO `suggestions_categ` VALUES
(1, 'catégorie par défaut');

-- --------------------------------------------------------

--
-- Structure de la table `suggestions_origine`
--

CREATE TABLE IF NOT EXISTS `suggestions_origine` (
  `origine` varchar(100) NOT NULL DEFAULT '',
  `num_suggestion` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `type_origine` int(3) UNSIGNED NOT NULL DEFAULT 0,
  `date_suggestion` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`origine`,`num_suggestion`,`type_origine`),
  KEY `i_origine` (`origine`,`type_origine`),
  KEY `i_num_suggestion` (`num_suggestion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `suggestions_source`
--

CREATE TABLE IF NOT EXISTS `suggestions_source` (
  `id_source` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle_source` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_source`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sur_location`
--

CREATE TABLE IF NOT EXISTS `sur_location` (
  `surloc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `surloc_libelle` varchar(255) NOT NULL DEFAULT '',
  `surloc_pic` varchar(255) NOT NULL DEFAULT '',
  `surloc_visible_opac` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `surloc_name` varchar(255) NOT NULL DEFAULT '',
  `surloc_adr1` varchar(255) NOT NULL DEFAULT '',
  `surloc_adr2` varchar(255) NOT NULL DEFAULT '',
  `surloc_cp` varchar(15) NOT NULL DEFAULT '',
  `surloc_town` varchar(100) NOT NULL DEFAULT '',
  `surloc_state` varchar(100) NOT NULL DEFAULT '',
  `surloc_country` varchar(100) NOT NULL DEFAULT '',
  `surloc_phone` varchar(100) NOT NULL DEFAULT '',
  `surloc_email` varchar(100) NOT NULL DEFAULT '',
  `surloc_website` varchar(100) NOT NULL DEFAULT '',
  `surloc_logo` varchar(100) NOT NULL DEFAULT '',
  `surloc_comment` text NOT NULL,
  `surloc_num_infopage` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `surloc_css_style` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`surloc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `system_template`
--

CREATE TABLE IF NOT EXISTS `system_template` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `comment` text NOT NULL,
  `image` varchar(250) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `system_template`
--

INSERT INTO `system_template` VALUES
(1, 'TemplateTitleCourseTitle', 'TemplateTitleCourseTitleDescription', 'coursetitle.gif', '\n		<head>\n		            	{CSS}\n		            	<style type=\"text/css\">\n		            	.gris_title         	{\n		            		color: silver;\n		            	}            	\n		            	h1\n		            	{\n		            		text-align: right;\n		            	}\n						</style>\n		  \n		            </head>\n		            <body>\n					<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n					<tbody>\n					<tr>			\n					<td style=\"vertical-align: middle; width: 50%;\" colspan=\"1\" rowspan=\"1\">\n						<h1>TITULUS 1<br>\n						<span class=\"gris_title\">TITULUS 2</span><br>\n						</h1>\n					</td>			\n					<td style=\"width: 50%;\">\n						<img style=\"width: 100px; height: 100px;\" alt=\"dokeos logo\" src=\"{COURSE_DIR}images/logo_dokeos.png\"></td>\n					</tr>\n					</tbody>\n					</table>\n					<p><br>\n					<br>\n					</p>\n					</body>\n		'),
(2, 'TemplateTitleTeacher', 'TemplateTitleTeacherDescription', 'yourinstructor.gif', '\n		<head>\n		                   {CSS}\n		                   <style type=\"text/css\">	            \n			            	.text\n			            	{	            	\n			            		font-weight: normal;\n			            	}\n							</style>\n		                </head>                    \n		                <body>\n							<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n							<tbody>\n							<tr>\n							<td></td>\n							<td style=\"height: 33%;\"></td>\n							<td></td>\n							</tr>\n							<tr>\n							<td style=\"width: 25%;\"></td>\n							<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 33%; text-align: right; font-weight: bold;\" colspan=\"1\" rowspan=\"1\">\n							<span class=\"text\">\n							<br>\n							Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis pellentesque.</span>\n							</td>\n							<td style=\"width: 25%; font-weight: bold;\">\n							<img style=\"width: 180px; height: 241px;\" alt=\"trainer\" src=\"{COURSE_DIR}images/trainer/trainer_case.png \"></td>\n							</tr>\n							</tbody>\n							</table>\n							<p><br>\n							<br>\n							</p>\n						</body>	\n		'),
(3, 'TemplateTitleLeftList', 'TemplateTitleListLeftListDescription', 'leftlist.gif', '\n		<head>\n			           {CSS}\n			       </head>		    \n				    <body>\n						<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n						<tbody>\n						<tr>\n						<td style=\"width: 66%;\"></td>\n						<td style=\"vertical-align: bottom; width: 33%;\" colspan=\"1\" rowspan=\"4\">&nbsp;<img style=\"width: 180px; height: 248px;\" alt=\"trainer\" src=\"{COURSE_DIR}images/trainer/trainer_reads.png \"><br>\n						</td>\n						</tr>\n						<tr align=\"right\">\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 66%;\">Lorem\n						ipsum dolor sit amet.\n						</td>\n						</tr>\n						<tr align=\"right\">\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 66%;\">\n						Vivamus\n						a quam.&nbsp;<br>\n						</td>\n						</tr>\n						<tr align=\"right\">\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 66%;\">\n						Proin\n						a est stibulum ante ipsum.</td>\n						</tr>\n						</tbody>\n						</table>\n					<p><br>\n					<br>\n					</p>\n					</body> \n		'),
(4, 'TemplateTitleLeftRightList', 'TemplateTitleLeftRightListDescription', 'leftrightlist.gif', '\n		\n		<head>\n			           {CSS}\n				    </head>\n					<body>\n						<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; height: 400px; width: 720px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n						<tbody>\n						<tr>\n						<td></td>\n						<td style=\"vertical-align: top;\" colspan=\"1\" rowspan=\"4\">&nbsp;<img style=\"width: 180px; height: 294px;\" alt=\"Trainer\" src=\"{COURSE_DIR}images/trainer/trainer_join_hands.png \"><br>\n						</td>\n						<td></td>\n						</tr>\n						<tr>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 33%; text-align: right;\">Lorem\n						ipsum dolor sit amet.\n						</td>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 33%; text-align: left;\">\n						Convallis\n						ut.&nbsp;Cras dui magna.</td>\n						</tr>\n						<tr>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 33%; text-align: right;\">\n						Vivamus\n						a quam.&nbsp;<br>\n						</td>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 33%; text-align: left;\">\n						Etiam\n						lacinia stibulum ante.<br>\n						</td>\n						</tr>\n						<tr>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 33%; text-align: right;\">\n						Proin\n						a est stibulum ante ipsum.</td>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 33%; text-align: left;\">\n						Consectetuer\n						adipiscing elit. <br>\n						</td>\n						</tr>\n						</tbody>\n						</table>\n					<p><br>\n					<br>\n					</p>\n					</body> \n		\n		'),
(5, 'TemplateTitleRightList', 'TemplateTitleRightListDescription', 'rightlist.gif', '\n			<head>\n			           {CSS}\n				    </head>\n				    <body style=\"direction: ltr;\">\n						<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n						<tbody>\n						<tr>\n						<td style=\"vertical-align: bottom; width: 50%;\" colspan=\"1\" rowspan=\"4\"><img style=\"width: 300px; height: 199px;\" alt=\"trainer\" src=\"{COURSE_DIR}images/trainer/trainer_points_right.png\"><br>\n						</td>\n						<td style=\"width: 50%;\"></td>\n						</tr>\n						<tr>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; text-align: left; width: 50%;\">\n						Convallis\n						ut.&nbsp;Cras dui magna.</td>\n						</tr>\n						<tr>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; text-align: left; width: 50%;\">\n						Etiam\n						lacinia.<br>\n						</td>\n						</tr>\n						<tr>\n						<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; text-align: left; width: 50%;\">\n						Consectetuer\n						adipiscing elit. <br>\n						</td>\n						</tr>\n						</tbody>\n						</table>\n					<p><br>\n					<br>\n					</p>\n					</body>  \n		'),
(6, 'TemplateTitleDiagram', 'TemplateTitleDiagramDescription', 'diagram.gif', '\n			<head>\n			                   {CSS}\n						    </head>\n						    \n							<body>\n							<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n							<tbody>\n							<tr>\n							<td style=\"background: transparent url({IMG_DIR}faded_grey.png ) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; text-align: left; height: 33%; width: 33%;\">\n							<br>\n							Etiam\n							lacinia stibulum ante.\n							Convallis\n							ut.&nbsp;Cras dui magna.</td>\n							<td colspan=\"1\" rowspan=\"3\">\n								<img style=\"width: 350px; height: 267px;\" alt=\"Alaska chart\" src=\"{COURSE_DIR}images/diagrams/alaska_chart.png \"></td>\n							</tr>\n							<tr>\n							<td colspan=\"1\" rowspan=\"1\">\n							<img style=\"width: 300px; height: 199px;\" alt=\"trainer\" src=\"{COURSE_DIR}images/trainer/trainer_points_right.png \"></td>\n							</tr>\n							<tr>\n							</tr>\n							</tbody>\n							</table>\n							<p><br>\n							<br>\n							</p>\n							</body>				    \n		'),
(7, 'TemplateTitleDesc', 'TemplateTitleCheckListDescription', 'description.gif', '\n		<head>\n			                   {CSS}\n						    </head>\n							<body>\n								<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n								<tbody>\n								<tr>\n								<td style=\"width: 50%; vertical-align: top;\">\n									<img style=\"width: 48px; height: 49px; float: left;\" alt=\"01\" src=\"{COURSE_DIR}images/small/01.png \" hspace=\"5\"><br>Lorem ipsum dolor sit amet<br><br><br>\n									<img style=\"width: 48px; height: 49px; float: left;\" alt=\"02\" src=\"{COURSE_DIR}images/small/02.png \" hspace=\"5\">\n									<br>Ut enim ad minim veniam<br><br><br>\n									<img style=\"width: 48px; height: 49px; float: left;\" alt=\"03\" src=\"{COURSE_DIR}images/small/03.png \" hspace=\"5\">Duis aute irure dolor in reprehenderit<br><br><br>\n									<img style=\"width: 48px; height: 49px; float: left;\" alt=\"04\" src=\"{COURSE_DIR}images/small/04.png \" hspace=\"5\">Neque porro quisquam est</td>\n									\n								<td style=\"vertical-align: top; width: 50%; text-align: right;\" colspan=\"1\" rowspan=\"1\">\n									<img style=\"width: 300px; height: 291px;\" alt=\"Gearbox\" src=\"{COURSE_DIR}images/diagrams/gearbox.jpg \"><br></td>\n								</tr><tr></tr>\n								</tbody>\n								</table>\n								<p><br>\n								<br>\n								</p>\n							</body>	\n		'),
(8, 'TemplateTitleCycle', 'TemplateTitleCycleDescription', 'cyclechart.gif', '\n		<head>\n			               {CSS}\n			               <style>\n			               .title\n			               {\n			               	color: white; font-weight: bold;\n			               }\n			               </style>                    \n					    </head>\n					    	\n					    	    \n					    <body>\n						<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"8\" cellspacing=\"6\">\n						<tbody>\n						<tr>\n							<td style=\"text-align: center; vertical-align: bottom; height: 10%;\" colspan=\"3\" rowspan=\"1\">\n								<img style=\"width: 250px; height: 76px;\" alt=\"arrow\" src=\"{COURSE_DIR}images/diagrams/top_arrow.png \">\n							</td>				\n						</tr>			\n						<tr>\n							<td style=\"height: 5%; width: 45%; vertical-align: top; background-color: rgb(153, 153, 153); text-align: center;\">\n								<span class=\"title\">Lorem ipsum</span>\n							</td>\n								\n							<td style=\"height: 5%; width: 10%;\"></td>					\n							<td style=\"height: 5%; vertical-align: top; background-color: rgb(153, 153, 153); text-align: center;\">\n								<span class=\"title\">Sed ut perspiciatis</span>\n							</td>\n						</tr>\n							<tr>\n								<td style=\"background-color: rgb(204, 204, 255); width: 45%; vertical-align: top;\">\n									<ul>\n										<li>dolor sit amet</li>\n										<li>consectetur adipisicing elit</li>\n										<li>sed do eiusmod tempor&nbsp;</li>\n										<li>adipisci velit, sed quia non numquam</li>\n										<li>eius modi tempora incidunt ut labore et dolore magnam</li>\n									</ul>\n						</td>			\n						<td style=\"width: 10%;\"></td>\n						<td style=\"background-color: rgb(204, 204, 255); width: 45%; vertical-align: top;\">\n							<ul>\n							<li>ut enim ad minim veniam</li>\n							<li>quis nostrud exercitation</li><li>ullamco laboris nisi ut</li>\n							<li> Quis autem vel eum iure reprehenderit qui in ea</li>\n							<li>voluptate velit esse quam nihil molestiae consequatur,</li>\n							</ul>\n							</td>\n							</tr>\n							<tr align=\"center\">\n							<td style=\"height: 10%; vertical-align: top;\" colspan=\"3\" rowspan=\"1\">\n							<img style=\"width: 250px; height: 76px;\" alt=\"arrow\" src=\"{COURSE_DIR}images/diagrams/bottom_arrow.png \">&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;\n						</td>\n						</tr>			\n						</tbody>\n						</table>\n						<p><br>\n						<br>\n						</p>\n						</body>	\n		'),
(9, 'TemplateTitleTimeline', 'TemplateTitleTimelineDescription', 'phasetimeline.gif', '\n		<head>\n		               {CSS} \n						<style>\n						.title\n						{				\n							font-weight: bold; text-align: center; 	\n						}			\n						</style>                \n				    </head>	\n				    \n				    <body>\n						<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"8\" cellspacing=\"5\">\n						<tbody>\n						<tr class=\"title\">				\n							<td style=\"vertical-align: top; height: 3%; background-color: rgb(224, 224, 224);\">Lorem ipsum</td>\n							<td style=\"height: 3%;\"></td>\n							<td style=\"vertical-align: top; height: 3%; background-color: rgb(237, 237, 237);\">Perspiciatis</td>\n							<td style=\"height: 3%;\"></td>\n							<td style=\"vertical-align: top; height: 3%; background-color: rgb(245, 245, 245);\">Nemo enim</td>\n						</tr>\n						\n						<tr>\n							<td style=\"vertical-align: top; width: 30%; background-color: rgb(224, 224, 224);\">\n								<ul>\n								<li>dolor sit amet</li>\n								<li>consectetur</li>\n								<li>adipisicing elit</li>\n							</ul>\n							<br>\n							</td>\n							<td>\n								<img style=\"width: 32px; height: 32px;\" alt=\"arrow\" src=\"{COURSE_DIR}images/small/arrow.png \">\n							</td>\n							\n							<td style=\"vertical-align: top; width: 30%; background-color: rgb(237, 237, 237);\">\n								<ul>\n									<li>ut labore</li>\n									<li>et dolore</li>\n									<li>magni dolores</li>\n								</ul>\n							</td>\n							<td>\n								<img style=\"width: 32px; height: 32px;\" alt=\"arrow\" src=\"{COURSE_DIR}images/small/arrow.png \">\n							</td>\n							\n							<td style=\"vertical-align: top; background-color: rgb(245, 245, 245); width: 30%;\">\n								<ul>\n									<li>neque porro</li>\n									<li>quisquam est</li>\n									<li>qui dolorem&nbsp;&nbsp;</li>\n								</ul>\n								<br><br>\n							</td>\n						</tr>\n						</tbody>\n						</table>\n					<p><br>\n					<br>\n					</p>\n					</body>\n		'),
(10, 'TemplateTitleTable', 'TemplateTitleCheckListDescription', 'table.gif', '\n		<head>\n		                   {CSS}\n		                   <style type=\"text/css\">\n						.title\n						{\n							font-weight: bold; text-align: center;\n						}\n						\n						.items\n						{\n							text-align: right;\n						}	\n		  				\n		\n							</style>\n		  \n					    </head>\n					    <body>\n					    <br />\n					   <h2>A table</h2>\n						<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px;\" border=\"1\" cellpadding=\"5\" cellspacing=\"0\">\n						<tbody>\n						<tr class=\"title\">\n							<td>City</td>\n							<td>2005</td>\n							<td>2006</td>\n							<td>2007</td>\n							<td>2008</td>\n						</tr>\n						<tr class=\"items\">\n							<td>Lima</td>\n							<td>10,40</td>\n							<td>8,95</td>\n							<td>9,19</td>\n							<td>9,76</td>\n						</tr>\n						<tr class=\"items\">\n						<td>New York</td>\n							<td>18,39</td>\n							<td>17,52</td>\n							<td>16,57</td>\n							<td>16,60</td>\n						</tr>\n						<tr class=\"items\">\n						<td>Barcelona</td>\n							<td>0,10</td>\n							<td>0,10</td>\n							<td>0,05</td>\n							<td>0,05</td>\n						</tr>\n						<tr class=\"items\">\n						<td>Paris</td>\n							<td>3,38</td>\n							<td >3,63</td>\n							<td>3,63</td>\n							<td>3,54</td>\n						</tr>\n						</tbody>\n						</table>\n						<br>\n						</body>\n		'),
(11, 'TemplateTitleAudio', 'TemplateTitleAudioDescription', 'audiocomment.gif', '\n		<head>\n		               {CSS}                    \n				    </head>\n		                   <body>\n							<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n							<tbody>\n							<tr>\n							<td>					\n							<div align=\"center\">\n							<span style=\"text-align: center;\">\n								<embed  type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" width=\"300\" height=\"20\" bgcolor=\"#FFFFFF\" src=\"{REL_PATH}main/inc/lib/mediaplayer/player.swf\" allowfullscreen=\"false\" allowscriptaccess=\"always\" flashvars=\"file={COURSE_DIR}audio/ListeningComprehension.mp3&amp;autostart=true\"></embed>\n		                    </span></div>     \n							\n							<br>\n							</td>\n							<td colspan=\"1\" rowspan=\"3\"><br>\n								<img style=\"width: 300px; height: 341px; float: right;\" alt=\"image\" src=\"{COURSE_DIR}images/diagrams/head_olfactory_nerve.png \"><br></td>\n							</tr>\n							<tr>\n							<td colspan=\"1\" rowspan=\"1\">\n								<img style=\"width: 180px; height: 271px;\" alt=\"trainer\" src=\"{COURSE_DIR}images/trainer/trainer_glasses.png\"><br></td>\n							</tr>\n							<tr>\n							</tr>\n							</tbody>\n							</table>\n							<p><br>\n							<br>\n							</p>\n							</body>	\n		'),
(12, 'TemplateTitleVideo', 'TemplateTitleVideoDescription', 'video.gif', '\n		<head>\n		            	{CSS}\n					</head>\n					\n					<body>\n					<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 720px; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n					<tbody>\n					<tr>\n					<td style=\"width: 50%; vertical-align: top;\">\n\n					<div style=\"text-align: center;\" id=\"player810625-parent\">\n					<div style=\"border-style: none; overflow: hidden; width: 320px; height: 240px; background-color: rgb(220, 220, 220);\">\n\n						<div id=\"player810625\">\n							<div id=\"player810625-config\" style=\"overflow: hidden; display: none; visibility: hidden; width: 0px; height: 0px;\">url={REL_PATH}main/default_course_document/video/flv/example.flv width=320 height=240 loop=false play=false downloadable=false fullscreen=true displayNavigation=true displayDigits=true align=left dispPlaylist=none playlistThumbs=false</div>\n						</div>\n\n						<embed\n							type=\"application/x-shockwave-flash\"\n							src=\"{REL_PATH}main/inc/lib/mediaplayer/player.swf\"\n							width=\"320\"\n							height=\"240\"\n							id=\"single\"\n							name=\"single\"\n							quality=\"high\"\n							allowfullscreen=\"true\"\n							flashvars=\"width=320&height=240&autostart=false&file={REL_PATH}main/default_course_document/video/flv/example.flv&repeat=false&image=&showdownload=false&link={REL_PATH}main/default_course_document/video/flv/example.flv&showdigits=true&shownavigation=true&logo=\"\n						/>\n\n					</div>\n					</div>\n\n					</td>\n					<td style=\"background: transparent url({IMG_DIR}faded_grey.png) repeat scroll center top; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; vertical-align: top; width: 50%;\">\n					<h3><br>\n					</h3>\n					<h3>Lorem ipsum dolor sit amet</h3>\n						<ul>\n						<li>consectetur adipisicing elit</li>\n						<li>sed do eiusmod tempor incididunt</li>\n						<li>ut labore et dolore magna aliqua</li>\n						</ul>\n					<h3>Ut enim ad minim veniam</h3>\n						<ul>\n						<li>quis nostrud exercitation ullamco</li>\n						<li>laboris nisi ut aliquip ex ea commodo consequat</li>\n						<li>Excepteur sint occaecat cupidatat non proident</li>\n						</ul>\n					</td>\n					</tr>\n					</tbody>\n					</table>\n					<p><br>\n					<br>\n					</p>\n					 <style type=\"text/css\">body{}</style><!-- to fix a strange bug appearing with firefox when editing this template -->\n					</body>\n		'),
(13, 'TemplateTitleFlash', 'TemplateTitleFlashDescription', 'flash.gif', '\n		<head>\n		               {CSS}                    \n				    </head>				    \n				    <body>\n				    <center>\n						<table style=\"background: transparent url({IMG_DIR}faded_blue_horizontal.png ) repeat scroll 0% 50%; -moz-background-clip: initial; -moz-background-origin: initial; -moz-background-inline-policy: initial; text-align: left; width: 100%; height: 400px;\" border=\"0\" cellpadding=\"15\" cellspacing=\"6\">\n						<tbody>\n							<tr>\n							<td align=\"center\">\n							<embed width=\"700\" height=\"300\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" src=\"{COURSE_DIR}flash/SpinEchoSequence.swf\" play=\"true\" loop=\"true\" menu=\"true\"></embed></span><br /> 				          													\n							</td>\n							</tr>\n						</tbody>\n						</table>\n						<p><br>\n						<br>\n						</p>\n					</center>\n					</body>\n		'),
(14, 'TemplateTitleTwoColumns', 'TemplateTitleTwoColumnsDescription', 'twocolumns.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent table_actions table_actions_rows\"><tr>\r\n				\r\n				    \r\n			    <td class=\"roundcell\"><h2>Essi bla </h2>\r\n                                   <p>Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam.</p>\r\n                                   <p>Gue verilisl del ullut prat wisl eraestrud dolumsan vendreet nostinim volum iustrud te dolobore magniamet ullamet utetum dunt wiscipit, volenis acin henit lum zzrit aci tin vel utpatem vulput adit lum zzriure delisi bla feu feummodit vel utetue eum dolor sequi ting ero et non volessequi euis nulluta tummolor sequis enismodit ex eugiamet in ut utet ulla facipis nos ad </p>\r\n                                 \r\n			    </td>\r\n                                             \r\n			\r\n				\r\n				\r\n				<td class=\"imagecenter\" valign=\"bottom\">\r\n				\r\n						<img src=\"{IMG_DIR}templates/instructor-hands.jpg\" />\r\n						\r\n				</td>\r\n				\r\n				\r\n				<td class=\"roundcell\"><h2>Essi bla </h2>\r\n                                   <p>Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam.</p>\r\n                                   <p>Ut alit lor inim volobore dit, quipit venissi bla ad dolor adit augiat. Pit landit iriliquisi te cons et in ut eu feuguerci blandipit alis dit atueriure magna faccum velenim velit wis eu feugait et adipis nis nullaor perosto dolorem ipit iurerci eraesto</p>\r\n                                   <p>Duip eugiate consed magna faci blam.</p>                                        \r\n			\r\n				</td>\r\n			</tr>\r\n			</table><!-- end table for the cells of content -->\r\n			\r\n			</td>\r\n			</tr>			\r\n		  </table> <!-- end white table for the course -->\r\n</body>'),
(15, 'TemplateTitleArrowChannel', 'TemplateTitleArrowChannelDescription', 'arrowchannel.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n			\r\n			<td>\r\n				<!--- tableau droit pour l\'illustration et la bulle--->\r\n				<table class=\"perso-and-buble\">\r\n				<tr><td id=\"buble-talk\">\r\n				  <p>Deliquat ute faccummy nullums andionsed et wisci bla consequis eraestrud magna adipsus cidunt ullam, consed erci blandipit landre.</p>\r\n				  </td>\r\n				</tr>\r\n				<tr><td><img src=\"{IMG_DIR}templates/instructor-puzzle.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				</table> \r\n				<!--- fin tableau droit pour lillustration et la bulle --->\r\n				</td>\r\n				<td>\r\n			   <!-- tableau fleche-->\r\n			   <table class=\"arrow-ch table_actions table_actions_rows\">				\r\n			   <tr valign=\"bottom\"><td class=\"arrow-ch-int\">\r\n			    <h2>Ad ming erit</h2>\r\n			   <p>Consequat nis elenibh eugiam zzrit utet.</p>\r\n			   </td></tr>\r\n			    <tr><td class=\"arrow-ch-int\">\r\n				 <h2>Ad ming erit</h2>\r\n			   <p>Consequat nis elenibh eugiam zzrit utet.</p>\r\n			   </td></tr>\r\n			    <tr><td class=\"arrow-ch-int\">\r\n				 <h2>Ad ming erit</h2>\r\n			   <p>Consequat nis elenibh eugiam zzrit utet.</p>\r\n			   </td></tr>\r\n			   </table>\r\n				<!-- fin tableau fleche-->		\r\n				\r\n				\r\n				 <!-- tableau fin -->\r\n			   <table class=\"ch-end\">				\r\n			   <tr><td>\r\n			   <h2>Magna corper sum iriurercipit </h2>\r\n			  \r\n				<p>Oborem qui tat diat.<br />\r\n				Consequat nis elenibh eugiam zzrit utet.</p>\r\n				</td></tr>\r\n			  \r\n			  \r\n				</table>\r\n				<!-- fin tableau fin -->\r\n				</td>\r\n				\r\n				\r\n				\r\n		  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course -->\r\n</body>'),
(16, 'TemplateTitleBiblio', 'TemplateTitleBiblioDescription', 'biblio.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\">\r\n				<tr><td>\r\n			\r\n				\r\n					   <table class=\"liste-livre table_actions table_actions_rows\">\r\n					   <tr><td class=\"book\">\r\n				    <h3>si erilit ad magna ad </h3>\r\n					 <h4 class=\"details\">ad lorem ipsum - ipsum</h4>\r\n					 <p>\r\n					 si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p><p>\r\n					 si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p></td></tr>\r\n					 <tr><td class=\"book\">\r\n				    <h3>si erilit ad magna ad </h3>\r\n					 <h4 class=\"details\">ad lorem ipsum - ipsum</h4>\r\n					 <p>\r\n					si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p></td></tr>\r\n					<tr><td class=\"book\">\r\n				    <h3>si erilit ad magna ad </h3>\r\n					 <h4 class=\"details\">ad lorem ipsum - ipsum</h4>\r\n					 <p>\r\n					 si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p>\r\n					 </td></tr>\r\n					 </table>\r\n					\r\n					 <td><img src=\"{IMG_DIR}templates/instructor-with-books.jpg\" /></td>\r\n                     </tr>\r\n		           </table>\r\n				  \r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td></tr>				\r\n		  </table><!-- end white table for the course --></body>		           '),
(17, 'TemplateTitleCertificate', 'TemplateTitleCertificateDescription', 'certificate.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				<!-- tableau gauche pour bulle + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>volenis acin henit lum zzrit aci tin vel feummodit vel utetue eum dolor sequi ting ero !</p>\r\n			      </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-certificate.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				\r\n				</table>\r\n				<!-- fin tableau gauche -->\r\n				</td>\r\n				\r\n				<td>\r\n				<!--- right table certificate --->\r\n				<table class=\"certif\" >\r\n				<tr><td>\r\n				<table class=\"certif-in\">\r\n				<tr>\r\n				<td>\r\n				<h2>Faccummy nim</h2>\r\n				<p>Ex et, qui estrud eu faccummy nostie dolorti nciliqu ipiscil utat</p><p>Qatuercil dolore dipit volorpe raeseni ssenis aliquatue.</p>\r\n				<p class=\"little\">Dunt am eummy nullaorem incillaortie te</p>\r\n				<p class=\"little-bold-right\">Dunt am te</p>\r\n				</td>\r\n				</tr>\r\n				</table></td></tr>\r\n				</table> \r\n				<!--- end certificate--->\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course -->\r\n</body>'),
(18, 'TemplateTitleCircularFourCells', 'TemplateTitleCircularFourCellsDescription', 'circularfourcells.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				<!-- tableau gauche pour bulle + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing.Pit landit iriliquisi te cons et in ut eu feuguerci blandipit alis dit atueriure magna faccum velenim velit wis eu feugait et adipis</p>\r\n			      </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-showleft.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				\r\n				</table>\r\n				<!-- fin tableau gauche -->\r\n				</td>\r\n				\r\n				<td>\r\n				<!--- tableau droit pour cercle --->\r\n				<table class=\"circular\" >\r\n				<!--HAUT--><tr><td><table class=\"circ-a1\"><tr><td><table id=\"circ-a2\">\r\n				  <tr><td width=\"150\">&nbsp;</td><td class=\"circular-item\"><p>Do ero eum iustrud</p>\r\n				     </td><td>&nbsp;</td></tr><tr><td colspan=\"3\" height=\"60px\">&nbsp;</td></tr></table></td></tr></table>\r\n				</td></tr>\r\n				<!-- MILIEU--><tr><td><table class=\"circ-a1\"><tr><td class=\"circular-item\"><p>Ci ex et landipit </p>\r\n				          </td><td>&nbsp;</td><td class=\"circular-item\"><p>Dolesequip essisit aut </p></td></tr>\r\n				</table>\r\n				</td></tr>\r\n				<!-- BAS--><tr><td><table class=\"circ-a1\"><tr><td><table id=\"circ-a3\">\r\n				  <tr><td colspan=\"3\" height=\"50px\">&nbsp;</td></tr>\r\n				  <tr><td width=\"160\">&nbsp;</td><td class=\"circular-item\"><p>Ut wis diamet in vulputpate </p>\r\n				      </td><td>&nbsp;</td></tr></table></td></tr></table>\r\n				</td></tr>\r\n				</table> \r\n				<!--- fin tableau droit pour cercle--->\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course -->\r\n</body>'),
(19, 'TemplateTitleCircularFiveCells', 'TemplateTitleCircularFiveCellsDescription', 'circularfivecells.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				<!-- tableau gauche pour bulle + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing. Ut alit ecte dolor iuscil enit numsand ionsenim niate do ent lorperate volor accum nissed molenis nulput in ero.</p>\r\n			      </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-showleft.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				\r\n				</table>\r\n				<!-- fin tableau gauche -->\r\n				</td>\r\n				\r\n				<td>\r\n				<!--- tableau droit pour cercle --->\r\n				<table class=\"circular\" >\r\n				<!--HAUT--><tr><td><table class=\"circ-a1\"><tr><td><table id=\"circ-a2\">\r\n				  <tr><td width=\"150\">&nbsp;</td><td class=\"circular-item\"><p>Do ero eum iustrud</p>\r\n				\r\n				      </td><td>&nbsp;</td></tr><tr><td colspan=\"5\" height=\"40px\">&nbsp;</td></tr></table></td></tr></table>\r\n				</td></tr>\r\n				<!-- MILIEU--><tr><td><table class=\"circ-a1\"><tr><td class=\"circular-item\"><p>Ci ex et landipit </p>\r\n				          </td><td colspan=\"3\">&nbsp;</td><td class=\"circular-item\"><p>Dolesequip essisit aut </p></td></tr>\r\n				</table>\r\n				</td></tr>\r\n				<!-- BAS--><tr><td><table class=\"circ-a1\"><tr><td><table id=\"circ-a3\">\r\n				  <tr><td colspan=\"5\" height=\"60px\">&nbsp;</td></tr>\r\n				  <tr><td width=\"60\">&nbsp;</td><td class=\"circular-item\"><p>Ut wis diamet in diamet </p>\r\n				     </td><td width=\"50\">&nbsp;</td><td class=\"circular-item\"><p>Ut wis diamet</p>\r\n				      </td><td width=\"60\">&nbsp;</td></tr></table></td></tr></table>\r\n				</td></tr>\r\n				</table> \r\n				<!--- fin tableau droit pour cercle--->\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(20, 'TemplateTitleDiagram', 'TemplateTitleDiagramDescription', 'diagram2.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\">\r\n				\r\n				<tr>\r\n				  \r\n				<td><img src=\"{IMG_DIR}templates/instructor-diagram.jpg\" alt=\"\" /></td>\r\n							\r\n				<td>\r\n				<table class=\"diag table_actions table_actions_rows\">\r\n                             \r\n                             <tr class=\"diagcorpus\">\r\n                               <td><h2>Essi bla </h2>\r\n							       <img src=\"{IMG_DIR}templates/diagram1.png\" />\r\n                                   <p>Essi bla accum zzrit.</p></td>\r\n								<td><h2>Essi bla </h2>\r\n								<img src=\"{IMG_DIR}templates/diagram2.png\" />\r\n                                   <p>Erostio dolore doloreet aliquat.</p></td>\r\n								<td><h2>Essi bla </h2>\r\n								<img src=\"{IMG_DIR}templates/diagram3.png\" />\r\n                                   <p>Duip eugiate consed magna faci blam.</p></td>\r\n                             </tr>\r\n                             <tr class=\"diagarrow\">\r\n                                <td><p>Essi bla </p>\r\n                                  </td>\r\n								<td><p>Essi bla </p>\r\n                                   </td>\r\n								<td><p>Essi bla </p>\r\n                                   </td>\r\n                             </tr>\r\n							 <tr class=\"diagcomment\">\r\n                                <td colspan=\"3\"><h2>Ea faciduis nullummy</h2><p>Essi bla Ut ad enissequat wismolum augait essenibh ea faciduis nullummy nulla alis nos nullam num dolum adigna faccum ilisl ex er sim vercipi scidunt la facinim deliqui scidunt alit praestie dignisit inibh eriustrud eraestinit nibh ectet essim duipissecte dit erit eummodo lendre vel er illa faccum irillaor </p>\r\n                               </td>\r\n                             </tr>\r\n                            \r\n               </table>                      \r\n\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(21, 'TemplateTitleFaq', 'TemplateTitleFaqDescription', 'faq.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n			<td >\r\n				<!---  left table for perso --->\r\n				\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing. Ut alit lor inim volobore dit, quipit venissi bla ad dolor adit.</p>\r\n			      </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-faq.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				\r\n				</table>\r\n				\r\n				<!-- end left table perso -->		\r\n				</td>\r\n				<td>\r\n			\r\n				<!-- tableau 1 gauche -->\r\n				<table class=\"right-table\">\r\n				<tr><td>\r\n				\r\n				  <!-- tableau gris container -->\r\n				     <table cellpadding=\"0\" cellspacing=\"0\" class=\"grey-frame\">\r\n                       <tr>\r\n                         <td>\r\n						 <!-- tableau de contenu haut degrade -->\r\n						 <table class=\"item table_actions table_actions_rows\">\r\n                             \r\n                             <tr>\r\n                               <td class=\"faq\"><h2>Mod magna feuisis elit ut wisim ipis nulla ?</h2>\r\n                                   <p>Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam. Tatue moluptatis ad enibh.</p>\r\n                               </td>\r\n								 \r\n                             </tr>\r\n							 <tr>\r\n                               <td class=\"faq\"><h2>Aute faccummy nim do od tio esse ? </h2>\r\n                                   <p>Cillaortie te dolortin utat adignis at, quip estrud dolorpe rostrud tet ut init at delit luptat, se exercin henim nonsequating ero dip essisl in et wisit wis erosto eu feugue consed moloreet vel eumsand.</p>\r\n                               </td>\r\n								 \r\n                             </tr>\r\n							 <tr>\r\n                               <td class=\"faq\"><h2>Qui estrud eu faccummy nostie dolorti ?</h2>\r\n                                   <p>Re eui eu feuipisim autem vendipsum zzrit dunt alisisl ip eros at diate mincilla amcon henibh elisi.</p>\r\n                                   </td>\r\n								 \r\n                             </tr>\r\n							 <tr>\r\n                               <td class=\"faq\"><h2>Isse magnismolore dolore con henim nummy ?</h2>\r\n                                   <p>It, consed ent ilis nullaore vel ullum volessenibh ex er se venit alis nulluptat. La feum aliquis ismoluptat ulput et atumsandrer ing ex euissi etummodo el ullan ulpute feum ilit nullaorem dolenit augait.</p>\r\n                                   </td>\r\n								 \r\n                             </tr>\r\n                           </table>\r\n                           <!-- fin tableau de contenu haut degrade -->                      \r\n						   </td>\r\n                       </tr>\r\n		          </table>\r\n				     <!-- fin du tableau gris containeur -->\r\n				</td>\r\n				</tr>\r\n				</table> \r\n				<!-- fin tableau 1 gauche -->\r\n				</td>				\r\n				\r\n				\r\n				\r\n			</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(22, 'TemplateTitleFrame', 'TemplateTitleFrameDescription', 'frame.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				\r\n					   <td>\r\n					   <table class=\"left-table-for-text table_actions table_actions_rows\">\r\n					   <tr><td>\r\n				    <h3>Si erilit ad magna ad </h3>\r\n					 <h4 class=\"details\">ad lorem ipsum - ipsum</h4>\r\n					 <p>\r\n					 Si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Ut alit lor inim volobore dit, quipit venissi bla ad dolor adit augiat. Pit landit iriliquisi te cons et in ut eu feuguerci blandipit alis dit atueriure magna faccum velenim velit wis eu feugait et adipis nis nullaor perosto dolorem ipit iurerci eraesto.</p>\r\n				    <p>In el do od deliquatio odit esequisit ipsummo dolessequat lorer aliquis eumsandio consequamcon ut ing et, quisse dipit ver.</p>\r\n				    <p> incillam eum iusci tate del ut lut wiscilit aute faccummy nim do od tio esse dolore venim vent nis augiamcon hendre feuis at. </p>\r\n				    <p>It, consed ent ilis nullaore vel ullum volessenibh ex er se venit alis nulluptat. Uptatue raestrud duisi.\r\nLa feum aliquis ismoluptat ulput et atumsandrer ing ex euissi etummodo el ullan ulpute feum ilit nullaorem dolenit augait.</p>\r\n				    </td></tr>\r\n					   </table>\r\n					</td>\r\n						 \r\n                     <td>\r\n						<table class=\"persoandframe\"><tr><td><img src=\"{IMG_DIR}templates/instructor-frame.jpg\" /></td>\r\n						</tr><tr><td class=\"frame-for-text\"><h2>Ea consenibh eugiam</h2>\r\n						<p>Ex et, qui estrud eu faccummy nostie dolorti nciliqu ipiscil utat, quatuercil dolore dipit volorpe raeseni ssenis aliquatue.</p>\r\n						<p>Em vel dolorer ciliqui smolor sequat. Put prat nit.</p></td></tr></table>\r\n					 </td>\r\n                       				\r\n			    </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course -->\r\n</body>'),
(23, 'TemplateTitleGallery', 'TemplateTitleGalleryDescription', 'gallery.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent2 table_actions table_actions_rows\">\r\n						<tr>\r\n						<td class=\"item-image-legende\">\r\n						<img src=\"{IMG_DIR}templates/little-placeholder-image.jpg\" />\r\n						<p>lorem ipsum and ipsum lorem ipsum and ipsum</p>\r\n						</td>\r\n						\r\n						<td class=\"item-image-legende\">\r\n						<img src=\"{IMG_DIR}templates/little-placeholder-image.jpg\" />\r\n						<p>lorem ipsum and ipsum lorem ipsum and ipsum</p>\r\n						</td>\r\n						\r\n						<td class=\"item-image-legende\">\r\n						<img src=\"{IMG_DIR}templates/little-placeholder-image.jpg\" />\r\n						<p>lorem ipsum and ipsum lorem ipsum and ipsum</p>\r\n						</td>\r\n						</tr>\r\n						<tr>						\r\n						<td class=\"item-image-legende\">\r\n						<img src=\"{IMG_DIR}templates/little-placeholder-image.jpg\" />\r\n						<p>lorem ipsum and ipsum lorem ipsum and ipsum</p>\r\n						</td>\r\n						\r\n						<td class=\"item-image-legende\">\r\n						<img src=\"{IMG_DIR}templates/little-placeholder-image.jpg\" />\r\n						<p>lorem ipsum and ipsum lorem ipsum and ipsum</p>\r\n						</td>\r\n						\r\n						<td class=\"item-image-legende\">\r\n						<img src=\"{IMG_DIR}templates/little-placeholder-image.jpg\" />\r\n						<p>lorem ipsum and ipsum lorem ipsum and ipsum</p>\r\n						</td>\r\n						</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(24, 'TemplateTitleGears', 'TemplateTitleGearsDescription', 'gears.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				<!-- tableau gauche pour bulle + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk-ud\"><p>si erilit ad magna ad dolorercing.Isse magnismolore dolore con henim nummy nulla.</p>\r\n			      </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-impulsion.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				\r\n				</table>\r\n				<!-- fin tableau gauche -->\r\n				</td>\r\n				\r\n				<td>\r\n				<!--- tableau droit pour rouages --->\r\n				<table class=\"rouages\" >\r\n				<tr>\r\n				<td id=\"bord-un\"></td>\r\n				<td id=\"rouage-un\">Er sum vulla am diamet</td>\r\n				<td id=\"bord-deux\"></td>\r\n				<td id=\"rouage-deux\">Quisci bla conullam zzrilit</td>\r\n				<td id=\"rouage-trois\">Ci ex et landipit nosto</td>\r\n				<td id=\"bord-trois\"></td>\r\n				</tr>\r\n				\r\n				</table> \r\n				<!--- fin tableau droit pour rouages--->\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(25, 'TemplateTitleGrowth', 'TemplateTitleGrowthDescription', 'growth.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				<!-- tableau gauche pour bulle + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing.</p><p>Ipisseq uissit lor secte faccumsandit ipsum diamcom modolortie.  nulput in ero exercipit wismodi.</p>\r\n			      </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-climbing.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				\r\n				</table>\r\n				<!-- fin tableau gauche -->\r\n				</td>\r\n				\r\n				<td>\r\n				<!--- tableau droit pour rouages --->\r\n				<table class=\"growth\" >\r\n			\r\n				<tr><td class=\"text-growth-stage\">\r\n				<table class=\"table_actions table_actions_rows\">\r\n				<tr><td style=\"padding-left:240px\"><p><span class=\"orange-bold\">Ipit num ip</span></p></td></tr>\r\n				<tr>\r\n				  <td style=\"padding-left:190px\">Esto ent feugiat</td>\r\n				</tr>\r\n				<tr><td style=\"padding-left:150px\">Magna facillu ptating</td></tr>\r\n				<tr><td style=\"padding-left:125px\">Vel eumsand rerat</td></tr>\r\n				<tr><td style=\"padding-left:100px\">Faccummy nim do od tio</td></tr>\r\n				<tr><td style=\"padding-left:80px\">Ex et, qui estrud eu faccummy</td></tr>\r\n				<tr><td style=\"padding-left:60px\">Ipisseq uissit lor secte</td></tr>\r\n				</table>\r\n				</td>\r\n				</tr>\r\n				\r\n				\r\n				</table> \r\n				<!--- fin tableau droit pour rouages--->\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>');
INSERT INTO `system_template` VALUES
(26, 'TemplateTitleImage', 'TemplateTitleImageDescription', 'image.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent table_actions table_actions_rows\"><tr>\r\n				<td>\r\n			\r\n				\r\n					   <!-- tableau image et sa legende -->\r\n					   <table class=\"image-and-legend\"><tr><td><img src=\"{IMG_DIR}templates/placeholder-image.jpg\" /></td></tr><tr><td class=\"legendingrey\"><p>Aliquis er in erostio dolore dolore et aliquat. Duip eugiate consed magna</p>\r\n					     </td></tr>\r\n						</table>\r\n						<!-- fin tableau image et sa legende -->\r\n				</td>\r\n						 \r\n                <td >\r\n						<h2>Essi bla </h2>\r\n						<p>Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam.</span> <span class=\"item-desc\">Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam.</p>\r\n					   <p> Duip eugiate consed magna faci blam. Essi bla accum zzrit ali.</p>\r\n					   <p>Consed magna faci blam.</p>\r\n					   <p>Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam. Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam.</p>\r\n                                      \r\n				</td>\r\n                </tr>\r\n			  </table>\r\n			  <!-- fin du tableau gris contenant le tableau texte et le tableau image -->\r\n\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(27, 'TemplateTitlePostIt', 'TemplateTitlePostItDescription', 'postit.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				<!-- tableau  post-it -->\r\n				<table class=\"post-it-table\">\r\n				<tr>\r\n				<td class=\"PI\" >\r\n				  <!-- tableau enchesse pour le coin droit -->\r\n				  <table class=\"PI-corner\">\r\n				    <tr><td>\r\n				  <h2>Vendre do dolorpe</h2>\r\n				  <p>Ulla conse feugait lor sustrud minit prat. Esto odolorpero con vendipsusto eum adit wisit, si erilit ad magna.			    </p>\r\n				\r\n				  <p>Isse magnismolore dolore con henim nummy nulla ad magna facin vel dolore dolese endre conse dolesse del euis nis dunt in henim quamcommy nim dolore veliquat, verit lum nonsequatuer ipis nostiscinibh ea cortio odo dip ea corperat in hendipisim ing eliqui.</p>\r\n				 \r\n		\r\n				  \r\n				  </td></tr></table>\r\n				  <!-- fin tableau enchesse -->\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- fin tableau post-it-->\r\n			  </td>\r\n				\r\n				<td class=\"imagecenter\">\r\n				<img src=\"{IMG_DIR}templates/instructor-writing.jpg\" alt=\"\" />\r\n				\r\n			  </td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(28, 'TemplateTitlePyramid', 'TemplateTitlePyramidDescription', 'pyramid.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n			     <table class=\"pyramide-background table_actions table_actions_rows\">				\r\n			   \r\n				 <tr><td style=\"font-size:0.8em; padding-top:80px;\">ad lorem ipsum</td></tr>\r\n				 <tr><td style=\"font-size:0.9em\">ad lorem ipsum</td></tr>\r\n				 <tr><td style=\"font-size:1em\">ad lorem ipsum</td></tr>\r\n				 <tr><td style=\"font-size:1.1em\">ad lorem ipsum</td></tr>\r\n				 <tr><td style=\"font-size:1.2em\">ad lorem ipsum</td></tr>\r\n				 <tr><td style=\"font-size:1.3em\">ad lorem ipsum</td></tr>\r\n				 <tr><td style=\"font-size:1.4em\">ad lorem ipsum</td></tr>\r\n				 <tr><td style=\"font-size:1.5em\">ad lorem ipsum</td></tr>\r\n				\r\n			\r\n				</table>\r\n\r\n				\r\n				</td>\r\n				\r\n				<td>\r\n				<table class=\"perso-and-buble\">\r\n				<tr><td id=\"buble-talk\"><p>\r\n				Riure con et vulluptat, veniam, consequamet, commolor iliquat dunt iureraessi.\r\nUptat. Ectem doloreet alis nonsed magna feuisim et at. Rit vullaore vullan.</p></td>\r\n				</tr>\r\n				<tr><td><img src=\"{IMG_DIR}templates/instructor-coming.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				</table> \r\n				<!--- fin tableau droit pour lillustration et la bulle --->\r\n				</td>\r\n				\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(29, 'TemplateTitleResult', 'TemplateTitleResultDescription', 'result.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\">\r\n				<tr><td>\r\n				<!-- left table buble + perso -->\r\n				<table class=\"perso-and-buble\" style=\"width:250px\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing ea consequis dolorpe raessequat.</p>\r\n				    </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-faq.jpg\" /></td>\r\n				</tr>\r\n				</table>\r\n				<!-- end left table -->\r\n				</td>\r\n				<td>\r\n				<table class=\"result\">\r\n                             \r\n                             \r\n                            \r\n                             <tr>\r\n                               <td class=\"add1\"><h2>Od eliquis at erostrud</h2>\r\n                                 <p>Duisl iureetue mod te molobor perilisl do con erit at pratue</p>\r\n                               </td>\r\n								   <td class=\"add2\"><h2>Sis nonsed etumsandre</h2><p>Eugait loreet praesse min vulpute tat</p>\r\n                                   </td>\r\n								   \r\n                             </tr>\r\n							 <tr>\r\n                               <td class=\"res\" colspan=\"2\"><img src=\"{IMG_DIR}templates/egal.png\" />\r\n                                   </td>						   \r\n								   \r\n                             </tr>\r\n							 <tr>\r\n                               <td class=\"res\" colspan=\"2\"><h2>Od eliquis erostrud</h2>\r\n							   <p>Duisl iureetue mod te molobor perilisl do con erit at pratue.</p><p>Nonsequ ipsusci esequam zzrillan eu faccum veliquamcor sisi tet ad molesti sismodolore facidunt niscinibh ese min et alisl utpat.</p>\r\n                                   </td>						   \r\n								   \r\n                             </tr>\r\n               </table>\r\n                         \r\n\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(30, 'TemplateTitleTextFourX', 'TemplateTitleTextFourXDescription', 'textxfour.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\">\r\n				<tr>\r\n				<td valign=\"top\">\r\n			\r\n				<!-- tableau gauche pour les 4 items -->\r\n				<table class=\"quatreitems table_actions table_actions_rows\" cellspacing=\"10\">\r\n				<tr>\r\n				  <td class=\"rond\" style=\"background-color:#e6e5e4; background-image:url(design/degrade3.jpg)\"><p>si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p>\r\n				    <p>si erilit ad magna ad </p>\r\n				    </td>\r\n					<td class=\"rond\" style=\"background-color:#d3d2d1; background-image:url(design/degrade2.jpg)\"><p>Deliquisim vero ex enibh ectem il in ullummodolor at.<br />\r\nRatem ipis at alit irit ipit wis nim in veliscipit</p>\r\n				    </td>				  \r\n				</tr>\r\n				<tr>\r\n					<td class=\"rond\" style=\"background-color:#d3d2d1; background-image:url(design/degrade2.jpg)\"><p>Ulla conse feugait lor sustrud minit prat. Esto odolorpero   con vendipsusto eum adit wisit, si erilit ad magna.</p>\r\n				    </td>\r\n					<td class=\"rond\" style=\"background-color:#e8e1dc; background-image:url(design/degrade1.jpg)\"><p>Essi bla accum zzrit aliquis er in erostio dolore   doloreet aliquat. Duip eugiate consed magna faci blam.</p>\r\n				    </td>	\r\n				</tr>\r\n				</table>\r\n				<!-- fin tableau gauche pour les 4 items -->\r\n				</td>\r\n				\r\n				<td class=\"imagecenter\">\r\n				<h2>Ad lorem ipsum</h2>\r\n				<p>Riure con et vulluptat, veniam, consequamet, commolor iliquat dunt iureraessi.\r\nUptat. Ectem doloreet alis nonsed magna feuisim et at. Rit vullaore vullan ut nulla commy nos num ver sim ver</p>\r\n				 <img src=\"{IMG_DIR}templates/carrefour.jpg\" alt=\"\" />\r\n				</td>\r\n				\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(31, 'TemplateTitleTitle', 'TemplateTitleTitleDescription', 'title.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr><td>\r\n	    \r\n				\r\n				<!-- tableau gris container -->\r\n				<table class=\"greyframetitle\">\r\n				<tr><td>\r\n					 <!-- tableau blanc container -->\r\n					 <table class=\"whiteframetitle\">\r\n                       <tr>\r\n					   <td><table><tr><td><img src=\"{IMG_DIR}templates/instructor-coming.jpg\" /></td></tr></table></td>\r\n                       <td><h1 class=\"orange\">Ad Lorem Ipsum</h1><h2 class=\"orange\">Essi bla accum zzrit aliquis</h2></td>\r\n                       </tr>\r\n					   </table>\r\n					   </td></tr>\r\n		              </table>\r\n				     <!-- fin du tableau blanc container -->\r\n				</td>\r\n				</tr>\r\n				</table><!-- fin du tableau gris container -->\r\n	  		</td>\r\n			</tr>\r\n                         \r\n\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(32, 'TemplateTitleSound', 'TemplateTitleSoundDescription', 'sound.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				<!-- tableau gauche pour bulle + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk-ud\"><p>si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p>\r\n				    </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-speaking.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				\r\n				</table>\r\n				<!-- fin tableau gauche -->\r\n				</td>\r\n				\r\n				<td>\r\n				\r\n				\r\n				     <!-- tableau gris container -->\r\n				     <table class=\"sound table_actions table_actions_rows\" >\r\n                        <tr><td class=\"readsound\"><img src=\"{IMG_DIR}templates/placeholder-son.jpg\" /></td></tr>                           \r\n                       <tr><td class=\"commentsound\"><h2>Isse magnismolore dolore con</h2>							   \r\n                                   <p>Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam.</p>\r\n                                   <p>Ulla conse feugait lor sustrud minit prat. Esto odolorpero con vendipsusto eum adit wisit, si erilit ad magna.</p>\r\n								   <p>Riure con et vulluptat, veniam, consequamet, commolor iliquat dunt iureraessi.\r\nUptat. Ectem doloreet alis nonsed magna feuisim et at. Rit vullaore vullan ut nulla commy nos num ver sim ver.</p></td></tr>\r\n					   \r\n                      </table>\r\n				     \r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(33, 'TemplateTitleSimpleBase', 'TemplateTitleSimpleBaseDescription', 'simplebase.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr><td>\r\n				<!-- left table buble + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p>\r\n				    </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-two.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				</table>\r\n				<!-- end left table -->\r\n				</td>\r\n				\r\n				<td>\r\n				<table class=\"base table_actions table_actions_rows\">\r\n                             \r\n                             <tr>\r\n							 <td>\r\n                              <h2>Essi bla </h2>\r\n							  <h3>Endrem zzrit dolorem in velit volor sustrud</h3>\r\n                                   <p>Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam.</p>\r\n								   <h4>Vel delessis nos nullandre</h4>\r\n								   <p>Re tat lutem nullaor ercing eugait loreet praesse min vulpute tat. Luptate tat aci enim quiscidui bla feuisis cipissecte cons non heniat lumsan vullut ut.</p>\r\n								   <h4>Velenis dipis dolor si</h4>\r\n								   <p>Aliquatem volore dolor sustio eugiat la cons nibh exercing ea facidui scipit iustie corem dolore erit ad magnibh et, consequis dit atum zzrilit landrerostin.</p>\r\n								   <h3>Etumsandre euguer adigna</h3>\r\n<p>Lore ming ex endre euis nullaor adit voloborero od eliquis erostrud dignit luptat. Ex ea facilismod tet acincip sustrud modiam, cons nonum init, sis nonsed etumsandre euguer adigna feuguer cidunt iuscilis num augait lore consequisi.</p></td>\r\n                             </tr>\r\n                             \r\n               </table>\r\n                         \r\n\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(34, 'TemplateTitleVideo320', 'TemplateTitleVideo320Description', 'video320.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				\r\n				\r\n				     <!-- tableau gris contenant le tableau video et le tableau texte -->\r\n				     \r\n					   <!-- tableau video et sa legende -->\r\n					   <table class=\"videoplace\"><tr><td><img src=\"{IMG_DIR}templates/little-placeholder-video.jpg\" /></td></tr><tr><td class=\"undervideo\"><p>Aliquis er in erostio dolore dolore et aliquat. Duip eugiate consed magna</p>\r\n					     </td></tr>\r\n						</table>\r\n						<!-- fin tableau video et sa legende -->\r\n						 </td>\r\n						 \r\n                         <td>\r\n						 <!-- tableau pour le texte a droite -->\r\n						 <table class=\"commentvideo\">\r\n                          \r\n                           <tr>\r\n                             <td><h2 class=\"orange\">Ad lorem ipsum</h2>\r\n							     <table><tr><td><p>Aliquis er in erostio dolore dolore et aliquat. Duip eugiate consed magna.</p>\r\n							           <p>Si tatet alit nullaor sum aut prat num illa facip etum quat verilit la faci te tat. Oborem qui tat diat. Ut alit lor inim volobore dit, quipit venissi bla ad dolor adit augiat. Pit landit iriliquisi te cons et in ut eu feuguerci blandipit alis dit atueriure magna faccum velenim velit wis eu feugait et adipis nis nullaor perosto dolorem ipit iurerci eraesto.</p></td><td><img src=\"{IMG_DIR}templates/instructor-projection.jpg\" /></td></tr></table>\r\n                           \r\n                             </td>\r\n                           </tr>\r\n                         </table>\r\n                           <!-- fin du tableau pour le texte a droite -->                         \r\n					\r\n\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(35, 'TemplateTitleVideo480', 'TemplateTitleVideo480Description', 'video480.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				\r\n				\r\n				     <!-- tableau gris contenant le tableau video et le tableau texte -->\r\n				     \r\n					   <!-- tableau video et sa l?gende -->\r\n					   <table class=\"videoplace480\"><tr><td><img src=\"{IMG_DIR}templates/placeholder-video2.jpg\" /></td></tr><tr><td class=\"undervideo\"><p>Aliquis er in erostio dolore dolore et aliquat. Duip eugiate consed magna.</p>\r\n					     </td></tr>\r\n						</table>\r\n						<!-- fin tableau video et sa legende -->\r\n						 </td>\r\n						 \r\n                         <td>\r\n						 <!-- tableau pour le texte a droite -->\r\n						 <table class=\"commentvideo\">\r\n                          \r\n                           <tr>\r\n                             <td><h2 class=\"orange\">Ad lorem ipsum</h2>\r\n							     <table><tr><td><p>Aliquis er in erostio dolore dolore et aliquat. Duip eugiate consed magna.</p>\r\n							           <p>Si tatet alit nullaor sum aut prat num illa facip etum quat verilit la faci te tat. Oborem qui tat diat. Ut alit lor inim volobore dit, quipit venissi bla ad dolor adit augiat. Pit landit iriliquisi te cons et in ut eu feuguerci.</p><p>Ommy nostionsed exeros esto eliqui bla facipsumsan volenit velestisl diat.</p></td><td><img src=\"{IMG_DIR}templates/instructor-projection.jpg\" /></td></tr></table>\r\n                           \r\n                             </td>\r\n                           </tr>\r\n                         </table>\r\n                           <!-- fin du tableau pour le texte a droite -->                         \r\n					\r\n\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(36, 'TemplateTitleTrueFalse', 'TemplateTitleTrueFalseDescription', 'truefalse.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\">\r\n				<tr>\r\n			    <td class=\"imagecenter\"><img src=\"{IMG_DIR}templates/instructor-truefalse.jpg\" /></td>				\r\n				<td>\r\n				   <!-- tableau mis en page -->\r\n				   <table class=\"tabletruefalse table_actions table_actions_rows\">\r\n				   <tr><th>Re tat lutem nullaor ercing</th><th>Cipissecte</th><th>Od eliquis</th>\r\n				   <tr>\r\n				   <td class=\"theQ\"><p>Luptate tat aci enim quiscidui bla feuisis cipissecte cons non heniat lumsan</p></td><td class=\"TF\"><img src=\"{IMG_DIR}templates/icone-V-QUIZ.png\" /></td><td class=\"TF\"></td>\r\n				   </tr>\r\n				   <tr>\r\n				   <td class=\"theQ\"><p>Lore ming ex endre euis nullaor adit voloborero od eliquis erostrud dignit luptat</p></td><td class=\"TF\"></td><td class=\"TF\"><img src=\"{IMG_DIR}templates/icone-V-QUIZ.png\" /></td>\r\n				   </tr>\r\n				   <tr>\r\n				   <td class=\"theQ\"><p>Faccum veliquamcor sisi tet ad molesti sismodolore facidunt niscinibh ese min et alisl utpat</p></td><td class=\"TF\"><img src=\"{IMG_DIR}templates/icone-V-QUIZ.png\" /></td><td class=\"TF\"><img src=\"{IMG_DIR}templates/icone-V-QUIZ.png\" /></td>\r\n				   </tr>\r\n				   <tr>\r\n				   <td class=\"theQ\"><p>Re tat lutem nullaor ercing eugait loreet praesse min vulpute ta</p></td><td class=\"TF\"><img src=\"{IMG_DIR}templates/icone-V-QUIZ.png\" /></td><td class=\"TF\"></td>\r\n				   </tr>\r\n				   <tr>\r\n				   <td class=\"theQ\"><p>Tem nis endions equat. Lestisl ut prat, sum zzrit, consequat</p></td><td class=\"TF\"><img src=\"{IMG_DIR}templates/icone-V-QUIZ.png\" /></td><td class=\"TF\"><img src=\"{IMG_DIR}templates/icone-V-QUIZ.png\" /></td\r\n				   </tr>			   \r\n				   </table>\r\n				   <!-- fin tableau mis en page -->				\r\n			  </td>\r\n			</tr>\r\n			</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n		  </td>\r\n		  </tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(37, 'TemplateTitleTable', 'TemplateTitleTableDescription', 'table2.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr><td>\r\n				 <!-- tableau mis en page -->\r\n				   <table class=\"the-tableau table_actions table_actions_rows\">\r\n				   <tr class=\"premiere\">\r\n				   <td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td>\r\n				   </tr>\r\n				   <tr class=\"ligne\">\r\n				   <td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td>\r\n				   </tr>\r\n				   <tr class=\"ligne\">\r\n				   <td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td>\r\n				   </tr>\r\n				   <tr class=\"ligne\">\r\n				   <td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td>\r\n				   </tr>\r\n				   <tr class=\"ligne\">\r\n				   <td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td><td>ad lorem ipsum</td>\r\n				   </tr>\r\n				   </table>\r\n				   <!-- fin tableau mis en page -->\r\n\r\n				</td>\r\n				</tr>\r\n				\r\n				<tr><td>\r\n				<table class=\"comments\"> \r\n				<tr>\r\n				<td>\r\n			\r\n				<!-- tableau post-it -->\r\n				<table class=\"post-it-table\">\r\n				<tr>\r\n				<td class=\"PI\">\r\n				  <!-- tableau enchesse pour le bord corne droit -->\r\n				  <table class=\"PI-corner\">\r\n				  <tr><td>\r\n				  <h2>LOREM IPSUM </h2>\r\n				  <p>Ulla conse feugait lor sustrud minit prat. Esto odolorpero con vendipsusto eum adit wisit, si erilit ad magna.			    </p>\r\n				  </td></tr></table>\r\n				  <!-- fin tableau enchenchesse pour le bord corne droit -->\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- fin tableau post-it -->\r\n			   </td>\r\n				\r\n				<td >\r\n				<img src=\"{IMG_DIR}templates/instructor-board.jpg\" alt=\"\" /></td></tr>\r\n				</td>				\r\n		  	   </tr>	\r\n			   </table>\r\n			\r\n			   </td></tr></table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(38, 'TemplateTitleProcess', 'TemplateTitleProcessDescription', 'process.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\">\r\n				<tr><td>\r\n						<table style=\"background-color:#dcdcde; margin:12px \">\r\n				  		<tr height=\"150\"  >\r\n						<td><table  class=\"first-item-process\"><tr><td>Lor sustrud minit prat</td></tr></table></td>\r\n						<td class=\"cell-item-process\"><table  class=\"item-process\"><tr><td>Esto odolorpero con vendipsusto</td></tr></table></td>\r\n						<td class=\"cell-item-process\"><table  class=\"item-process\"><tr><td><p>Vendipsusto eum adit wisit, si erilit ad magna</p>\r\n						 <p>&nbsp;</p></td></tr></table></td>\r\n						<td class=\"cell-item-process\"><table  class=\"item-process\"><tr><td>Si erilit ad magna.</td></tr></table></td>\r\n						<td class=\"cell-item-process\"><table  class=\"item-process\"><tr><td>Si erilit ad magna.</td></tr></table></td>\r\n						<td width=\"40\" style=\"background-image:url(design/process-end.jpg); background-repeat:no-repeat; background-position:center right\">&nbsp;</td>\r\n				  		</tr>\r\n				       </table>\r\n				</td></tr>\r\n				<tr><td><table class=\"comments\">\r\n                  <tr>\r\n                    <td><!-- tableau post-it -->\r\n                        <table class=\"post-it-table\">\r\n                          <tr>\r\n                            <td class=\"PI\"><!-- tableau enchesse pour le bord corne droit -->\r\n                                <table class=\"PI-corner\">\r\n                                  <tr>\r\n                                    <td><h2>LOREM IPSUM </h2>\r\n                                        <p>Ulla conse feugait lor sustrud minit prat. Esto odolorpero con vendipsusto eum adit wisit, si erilit ad magna.</p>\r\n                                      <p>Riure con et vulluptat, veniam, consequamet, commolor iliquat dunt iureraessi.\r\n                                        Uptat. Ectem doloreet alis nonsed magna feuisim et at. Rit vullaore vullan ut nulla commy nos num ver sim ver.</p></td>\r\n                                  </tr>\r\n                                </table>\r\n                              <!-- fin tableau enchesse pour le bord corne droit -->                            </td>\r\n                          </tr>\r\n                        </table>\r\n                      <!-- fin tableau post-it -->                    \r\n					</td>\r\n                    <td class=\"imagecenter\"><img src=\"{IMG_DIR}templates/instructor-analysis.jpg\" /> </td>\r\n                  </tr>\r\n                </table></td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(39, 'TemplateTitlePhases', 'TemplateTitlePhasesDescription', 'phases.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\">\r\n				<tr><td>\r\n				<!-- left table buble + perso -->\r\n				<table class=\"perso-and-buble\" style=\"width:250px\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p>\r\n				    </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-two.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				</table>\r\n				<!-- end left table -->\r\n				</td>\r\n				<td>\r\n				<table class=\"phases table_actions table_actions_columns\">\r\n                             \r\n                             <tr>\r\n                               <td class=\"phase\"><h2>CONS NOMUM</h2>\r\n                                   </td>\r\n								   <td class=\"phase\"><h2>ESSI BLA</h2>\r\n                                   </td>\r\n								   <td class=\"phase\"><h2>SUM IN HENIM</h2>\r\n                                   </td>\r\n                             </tr>\r\n                            \r\n                             <tr>\r\n                               <td class=\"phaseresult\"><h2>Od eliquis erostrud</h2>\r\n							   <p>Duisl iureetue mod te molobor perilisl do con erit at pratue</p>\r\n                                   </td>\r\n								   <td class=\"phaseresult\"><h2>Sis nonsed etumsandre</h2><p>Eugait loreet praesse min vulpute tat</p>\r\n                                   </td>\r\n								   <td class=\"phaseresult\"><h2>Dionsed te commy</h2><p>Luptate tat aci enim quiscidui bla feuisis cipissecte cons non heniat lumsan</p>\r\n                                   </td>\r\n                             </tr>\r\n               </table>\r\n                         \r\n\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(40, 'TemplateTitleMethodology', 'TemplateTitleMethodologyDescription', 'methodology.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\">\r\n				\r\n				<tr>						\r\n				<td>\r\n				<table class=\"methodology table_actions table_actions_columns\">\r\n                             \r\n                             <tr>\r\n                               <td class=\"methofirst\">\r\n							       <ul>\r\n								   <li>Od eliquis erostrud</li> <li>Re tat lutem nullaor ercing eugait loreet</li> <li>Corem dolore erit ad magnibh et, vel ute tatum ad te ea ad modolor </li></td>\r\n								<td class=\"methoarrow\">\r\n								<img src=\"{IMG_DIR}templates/little-placeholder-image150.jpg\" />\r\n								<h2>Veliquamcor sisi</h2>\r\n								  <p>Erostio dolore doloreet aliquat.</p>\r\n								  <p>Re tat lutem nullaor ercing eugait loreet.</p></td>\r\n								<td class=\"methoarrow\">\r\n								<img src=\"{IMG_DIR}templates/little-placeholder-image150.jpg\" />\r\n								<h2>Sis etumsandre</h2>\r\n								  <p>Duip eugiate consed magna faci blam.</p>\r\n								  <p>Nonsequ ipsusci esequam zzrillan eu.</p></td>\r\n								  <td class=\"methoarrow\">\r\n								  <img src=\"{IMG_DIR}templates/little-placeholder-image150.jpg\" />\r\n								  <h2>Essi bla </h2>\r\n								  <p>Duip eugiate consed magna faci blam.</p>\r\n								  <p>Ut ad enissequat wismolum augait.</p></td>\r\n                             </tr>\r\n                             \r\n							 \r\n                            \r\n               </table>                      \r\n\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n				<tr><td><table class=\"cellscontent\"><tr><td><img src=\"{IMG_DIR}templates/instructor-coming.jpg\"/></td><td><h2>Ea faciduis nullummy</h2><p>Essi bla Ut ad enissequat wismolum augait essenibh ea faciduis nullummy nulla alis nos nullam num dolum adigna faccum ilisl ex er sim vercipi scidunt la facinim deliqui scidunt alit praestie dignisit inibh eriustrud eraestinit nibh ectet essim duipissecte dit erit eummodo lendre vel er illa faccum irillaor </p></td></tr></table></td></tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(41, 'TemplateTitleItemsList', 'TemplateTitleItemsListDescription', 'itemslist.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr><td>\r\n				<!-- left table buble + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p>\r\n				    </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-two.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				</table>\r\n				<!-- end left table -->\r\n				</td>\r\n				\r\n				<td>\r\n				<table class=\"items table_actions table_actions_rows\">\r\n                             \r\n                             <tr>\r\n                               <td class=\"arrow\"><h2>Essi bla </h2>\r\n                                   <p>Essi bla accum zzrit aliquis er in erostio dolore doloreet aliquat. Duip eugiate consed magna faci blam.</p></td>\r\n                             </tr>\r\n                             <tr>\r\n                               <td class=\"arrow\"><h2>Deliquissim vero </h2>\r\n                                   <p>Deliquisim vero ex enibh ectem il in ullummodolor at.<br />\r\n                                     Ratem ipis at alit irit ipit wis nim in veliscipit </p></td>\r\n                             </tr>\r\n                             <tr>\r\n                               <td class=\"arrow\"><h2>Ulla conse </h2>\r\n                                   <p>Ulla conse feugait lor sustrud minit prat. Esto odolorpero con vendipsusto eum adit wisit, si erilit ad magna.</p></td>\r\n                             </tr>\r\n               </table>\r\n                         \r\n\r\n				</td>\r\n				</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(42, 'TemplateTitleK', 'TemplateTitleKDescription', 'k.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n			    <td class=\"illusleft\"><img src=\"{IMG_DIR}templates/instructor-analysis.jpg\" /></td>				\r\n				<td>\r\n				   <!-- tableau mis en page -->\r\n				   <table class=\"the-tableau table_actions table_actions_rows\" id=\"the-tableau-K\">\r\n				   <tr class=\"ligne-K\">\r\n				   <td class=\"theK\"><h3>ad lorem ipsum</h3></td><td class=\"expl\">\r\n				   <h4>Ci ex et landipit nosto dolor sectet vel il dolore molore duisisit, quis exer</h4>\r\n				   <ul>\r\n				     <li>Velestrud mod dionsequate dolor.</li>\r\n				     <li>Iril ipisse magna faccum ex eugiatum dolesequip.</li></ul>\r\n				   \r\n				   </td>\r\n				   </tr>\r\n				   <tr class=\"ligne-K\">\r\n				   <td class=\"theK\"><h3>ad lorem ipsum</h3></td><td class=\"expl\">\r\n				   <h4>Onsequi smodolore velit ullan eugiam enim do od modolorem vel ut aliquis</h4>\r\n				   <p>Deliquisim vero ex enibh ectem il in ullummodolor at.</p></td>\r\n				   </tr>\r\n				   <tr class=\"ligne-K\">\r\n				   <td class=\"theK\"><h3>ad lorem ipsum</h3></td><td class=\"expl\">\r\n				   <h4>Magna corper sum iriurercipit lortisisi</h4>\r\n				  <ul>\r\n				  <li>Er sum vulla am diamet nisim irit, quisci bla</li>\r\n				  <li>Consequat nis elenibh eugiam zzrit utet do ero eum.</li>\r\n				  </ul>\r\n				  </td>\r\n				   </tr>\r\n				  \r\n				   \r\n				   </table>\r\n				   <!-- fin tableau mis en page -->\r\n				\r\n			  </td>\r\n			</tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(43, 'TemplateTitleMap', 'TemplateTitleMapDescription', 'map.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			\r\n				<!-- tableau gauche pour bulle + perso -->\r\n				<table class=\"perso-and-buble\">\r\n				<tr>\r\n				  <td id=\"buble-talk\"><p>si erilit ad magna ad dolorercing ea consequis dolorpe raessequat. Si erilit ad magna ad dolorercing.</p>\r\n				    </td>\r\n				  \r\n				</tr>\r\n				<tr>\r\n				<td><img src=\"{IMG_DIR}templates/instructor-path.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				\r\n				</table>\r\n				<!-- fin tableau gauche -->\r\n				</td>\r\n				\r\n				<td>\r\n				<!--- tableau droit pour la map --->\r\n				\r\n				     <table class=\"map table_actions table_actions_rows\">\r\n                             <tr>\r\n                               <td class=\"part\"><h2>Essi bla </h2>\r\n							   <h3>Eer in erostio dolore</h3>\r\n                                   <h4>Essi bla accum zzrit aliquis</h4><ul><li>Er in erostio dolore doloreet aliquat</li><li>Duip eugiate consed magna faci blam</li></ul>\r\n								   \r\n								<h3>Re eui eu feuipisim autem</h3>\r\n                                   <h4>zzrit dunt alisisl</h4><ul><li>Re eui eu feuipisim autem vendipsum </li><li>Nostie dolorti nciliqu ipiscil utat</li><li>Quisse dipit ver incillam eum iusci </li></ul>\r\n								</td>\r\n                             </tr>\r\n                             <tr>\r\n                               <td class=\"part\"><h2>Esent irilisi blaor sisi</h2>\r\n                                 \r\n								   \r\n								<h3>Faccummy nim do od tio esse</h3>\r\n                                   <h4>Ut vero conullam</h4><ul><li>Ex et, qui estrud eu faccummy nostie dolorti nciliqu ipiscil</li><li>Em vel dolorer ciliqui smolor sequat</li></ul></td>\r\n                             </tr>\r\n				</table> \r\n				<!--- fin tableau droit pour la map\"--->\r\n				</td>\r\n				\r\n			  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course --></body>'),
(44, 'TemplateTitleTextArrow', 'TemplateTitleTextArrowDescription', 'textarrowthree.gif', '<head>\r\n{CSS}\r\n</head>\r\n\r\n<body>\r\n<div id=\"header\">\r\n<div id=\"fixedscreen\">\r\n<div id=\"headerinner\"><img src=\"{IMG_DIR}templates/icone-HOME.png\" /></div>\r\n<div class=\"roundframe\">\r\n\r\n	    <!-- white table for the course -->\r\n		<table class=\"white\"> \r\n		<tr><td><h1> LOREM IPSUM</h1></td></tr>\r\n		<tr>\r\n				<td>\r\n				<!-- table for the cells of content -->\r\n			    <table class=\"cellscontent\"><tr>\r\n				<td>\r\n			   <!-- tableau fleche1 -->\r\n			   <table class=\"grey-arrow\">				\r\n			   <tr><td>\r\n			   <h2>Ad ming erit</h2>\r\n			   <p>Consequat nis elenibh eugiam zzrit utet do ero eum iustrud dit alisisisit ad ming erit nim iure doloreetue doloreet nim dipit vulput dolorem venibh etum.</p>\r\n			   </td></tr>\r\n			   <tr><td class=\"fleche-grise\"></td></tr>\r\n			  \r\n				</table>\r\n				<!-- fin tableau fleche1 -->\r\n				 <!-- tableau fleche2 -->\r\n			   <table class=\"grey-arrow\">				\r\n			   <tr><td>\r\n			   <h2>Aut vel ex essequam veriustrud</h2>\r\n			     <p>Iril ipisse magna faccum ex eugiatum dolesequip essisit aut vel ex essequam veriustrud tatie mincip elisisl incip eliquip sustrud mincip ea feugue feuis. </p>\r\n			</td></tr>\r\n			   <tr>\r\n			     <td class=\"fleche-grise\"></td>\r\n			   </tr>\r\n			  \r\n				</table>\r\n				<!-- fin tableau fleche2 -->\r\n				\r\n				 <!-- tableau 3 -->\r\n			   <table class=\"dark-grey\">				\r\n			   <tr><td>\r\n			   <h2>Magna corper sum iriurercipit lortisisi</h2>\r\n			  \r\n<p>Si tatet alit nullaor sum aut prat num illa facip etum quat verilit la faci te tat. Oborem qui tat diat.</p>\r\n			 \r\n			   </td></tr>\r\n			  \r\n			  \r\n				</table>\r\n				<!-- fin tableau 3 -->\r\n				</td>\r\n				\r\n				<td>\r\n				<!--- tableau droit pour l\'illustration et la bulle--->\r\n				<table class=\"perso-and-buble\">\r\n				<tr><td id=\"buble-talk\">\r\n				<p>Deliquat ute faccummy nullums andionsed et wisci bla consequis eraestrud magna adipsus cidunt ullam, consed erci blandipit landre.</p></td>\r\n				</tr>\r\n				<tr><td><img src=\"{IMG_DIR}templates/instructor-3fingers.jpg\" alt=\"\" /></td>\r\n				</tr>\r\n				</table> \r\n				<!--- fin tableau droit pour lillustration et la bulle --->\r\n				</td>\r\n				\r\n		  </tr>\r\n				</table>\r\n				<!-- end table for the cells of content -->\r\n				\r\n				</td>\r\n				</tr>\r\n								\r\n		  </table><!-- end white table for the course -->\r\n	\r\n</div><!-- end roundframe -->\r\n</div><!-- end fixedscreen -->\r\n</div> <!-- end header -->\r\n</body>');

-- --------------------------------------------------------

--
-- Structure de la table `sys_announcement`
--

CREATE TABLE IF NOT EXISTS `sys_announcement` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `visible_teacher` tinyint(4) NOT NULL DEFAULT 0,
  `visible_student` tinyint(4) NOT NULL DEFAULT 0,
  `visible_guest` tinyint(4) NOT NULL DEFAULT 0,
  `title` varchar(250) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `lang` varchar(70) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sys_calendar`
--

CREATE TABLE IF NOT EXISTS `sys_calendar` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text DEFAULT NULL,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `taches`
--

CREATE TABLE IF NOT EXISTS `taches` (
  `id_tache` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_planificateur` int(11) DEFAULT NULL,
  `start_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` varchar(128) DEFAULT NULL,
  `msg_statut` blob DEFAULT NULL,
  `commande` int(8) NOT NULL DEFAULT 0,
  `next_state` int(8) NOT NULL DEFAULT 0,
  `msg_commande` blob DEFAULT NULL,
  `indicat_progress` int(3) DEFAULT NULL,
  `rapport` text DEFAULT NULL,
  `id_process` int(8) DEFAULT NULL,
  PRIMARY KEY (`id_tache`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `taches_docnum`
--

CREATE TABLE IF NOT EXISTS `taches_docnum` (
  `id_tache_docnum` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tache_docnum_nomfichier` varchar(255) NOT NULL,
  `tache_docnum_mimetype` varchar(255) NOT NULL,
  `tache_docnum_data` mediumblob NOT NULL,
  `tache_docnum_extfichier` varchar(20) DEFAULT NULL,
  `tache_docnum_repertoire` int(8) DEFAULT NULL,
  `tache_docnum_path` text NOT NULL,
  `num_tache` int(11) NOT NULL,
  PRIMARY KEY (`id_tache_docnum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `taches_type`
--

CREATE TABLE IF NOT EXISTS `taches_type` (
  `id_type_tache` int(11) UNSIGNED NOT NULL,
  `parameters` text NOT NULL,
  `timeout` int(11) NOT NULL DEFAULT 5,
  `histo_day` int(11) NOT NULL DEFAULT 7,
  `histo_number` int(11) NOT NULL DEFAULT 3,
  `restart_on_failure` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `alert_mail_on_failure` varchar(255) DEFAULT '',
  PRIMARY KEY (`id_type_tache`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `field_id` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id_tag` mediumint(8) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(200) NOT NULL DEFAULT '',
  `num_notice` mediumint(8) NOT NULL DEFAULT 0,
  `user_code` varchar(50) NOT NULL DEFAULT '',
  `dateajout` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `templates`
--

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `course_code` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ref_doc` int(11) NOT NULL,
  `image` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `thesaurus`
--

CREATE TABLE IF NOT EXISTS `thesaurus` (
  `id_thesaurus` int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle_thesaurus` varchar(255) NOT NULL DEFAULT '',
  `langue_defaut` varchar(5) NOT NULL DEFAULT 'fr_FR',
  `active` char(1) NOT NULL DEFAULT '1',
  `opac_active` char(1) NOT NULL DEFAULT '1',
  `num_noeud_racine` int(9) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_thesaurus`),
  UNIQUE KEY `libelle_thesaurus` (`libelle_thesaurus`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `thesaurus`
--

INSERT INTO `thesaurus` VALUES
(1, 'Thésaurus Défaut', 'fr_FR', '1', '1', 1);

-- --------------------------------------------------------

--
-- Structure de la table `thresholds`
--

CREATE TABLE IF NOT EXISTS `thresholds` (
  `id_threshold` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `threshold_label` varchar(255) NOT NULL DEFAULT '',
  `threshold_amount` float NOT NULL DEFAULT 0,
  `threshold_amount_tax_included` int(1) NOT NULL DEFAULT 0,
  `threshold_footer` text NOT NULL,
  `threshold_num_entity` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_threshold`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `titres_uniformes`
--

CREATE TABLE IF NOT EXISTS `titres_uniformes` (
  `tu_id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tu_name` varchar(255) NOT NULL DEFAULT '',
  `tu_tonalite` varchar(255) NOT NULL DEFAULT '',
  `tu_comment` text NOT NULL,
  `index_tu` text NOT NULL,
  `tu_import_denied` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tu_num_author` bigint(11) UNSIGNED NOT NULL DEFAULT 0,
  `tu_forme` varchar(255) NOT NULL DEFAULT '',
  `tu_date` varchar(50) NOT NULL DEFAULT '',
  `tu_date_date` date NOT NULL DEFAULT '0000-00-00',
  `tu_sujet` text NOT NULL,
  `tu_lieu` varchar(255) NOT NULL DEFAULT '',
  `tu_histoire` text DEFAULT NULL,
  `tu_caracteristique` text DEFAULT NULL,
  `tu_public` varchar(255) NOT NULL DEFAULT '',
  `tu_contexte` text DEFAULT NULL,
  `tu_coordonnees` varchar(255) NOT NULL DEFAULT '',
  `tu_equinoxe` varchar(255) NOT NULL DEFAULT '',
  `tu_completude` int(2) UNSIGNED NOT NULL DEFAULT 0,
  `tu_tonalite_marclist` varchar(5) NOT NULL DEFAULT '',
  `tu_forme_marclist` varchar(5) NOT NULL DEFAULT '',
  `tu_oeuvre_nature` varchar(3) NOT NULL DEFAULT 'a',
  `tu_oeuvre_type` varchar(3) NOT NULL DEFAULT 'a',
  `tu_oeuvre_nature_nature` varchar(255) NOT NULL DEFAULT 'original',
  PRIMARY KEY (`tu_id`),
  KEY `i_tu_tu_oeuvre_type` (`tu_oeuvre_type`),
  KEY `i_tu_tu_oeuvre_nature` (`tu_oeuvre_nature`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_c_browsers`
--

CREATE TABLE IF NOT EXISTS `track_c_browsers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `browser` varchar(255) NOT NULL DEFAULT '',
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_c_countries`
--

CREATE TABLE IF NOT EXISTS `track_c_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(40) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `track_c_countries`
--

INSERT INTO `track_c_countries` VALUES
(1, 'ac', 'Ascension (ile)', 0),
(2, 'ad', 'Andorre', 0),
(3, 'ae', 'Emirats  Arabes Unis', 0),
(4, 'af', 'Afghanistan', 0),
(5, 'ag', 'Antigua et Barbuda', 0),
(6, 'ai', 'Anguilla', 0),
(7, 'al', 'Albanie', 0),
(8, 'am', 'Arménie', 0),
(9, 'an', 'Antilles Neerlandaises', 0),
(10, 'ao', 'Angola', 0),
(11, 'aq', 'Antarctique', 0),
(12, 'ar', 'Argentine', 0),
(13, 'as', 'American Samoa', 0),
(14, 'au', 'Australie', 0),
(15, 'aw', 'Aruba', 0),
(16, 'az', 'Azerbaijan', 0),
(17, 'ba', 'Bosnie Herzegovine', 0),
(18, 'bb', 'Barbade', 0),
(19, 'bd', 'Bangladesh', 0),
(20, 'be', 'Belgique', 0),
(21, 'bf', 'Burkina Faso', 0),
(22, 'bg', 'Bulgarie', 0),
(23, 'bh', 'Bahrain', 0),
(24, 'bi', 'Burundi', 0),
(25, 'bj', 'Benin', 0),
(26, 'bm', 'Bermudes', 0),
(27, 'bn', 'Brunei Darussalam', 0),
(28, 'bo', 'Bolivie', 0),
(29, 'br', 'Brésil', 0),
(30, 'bs', 'Bahamas', 0),
(31, 'bt', 'Bhoutan', 0),
(32, 'bv', 'Bouvet (ile)', 0),
(33, 'bw', 'Botswana', 0),
(34, 'by', 'Biélorussie', 0),
(35, 'bz', 'Bélize', 0),
(36, 'ca', 'Canada', 0),
(37, 'cc', 'Cocos (Keeling) iles', 0),
(38, 'cd', 'Congo,(République démocratique du)', 0),
(39, 'cf', 'Centrafricaine (République )', 0),
(40, 'cg', 'Congo', 0),
(41, 'ch', 'Suisse', 0),
(42, 'ci', 'Cote d\'Ivoire', 0),
(43, 'ck', 'Cook (iles)', 0),
(44, 'cl', 'Chili', 0),
(45, 'cm', 'Cameroun', 0),
(46, 'cn', 'Chine', 0),
(47, 'co', 'Colombie', 0),
(48, 'cr', 'Costa Rica', 0),
(49, 'cu', 'Cuba', 0),
(50, 'cv', 'Cap Vert', 0),
(51, 'cx', 'Christmas (ile)', 0),
(52, 'cy', 'Chypre', 0),
(53, 'cz', 'Tchéque (République)', 0),
(54, 'de', 'Allemagne', 0),
(55, 'dj', 'Djibouti', 0),
(56, 'dk', 'Danemark', 0),
(57, 'dm', 'Dominique', 0),
(58, 'do', 'Dominicaine (république)', 0),
(59, 'dz', 'Algérie', 0),
(60, 'ec', 'Equateur', 0),
(61, 'ee', 'Estonie', 0),
(62, 'eg', 'Egypte', 0),
(63, 'eh', 'Sahara Occidental', 0),
(64, 'er', 'Erythrée', 0),
(65, 'es', 'Espagne', 0),
(66, 'et', 'Ethiopie', 0),
(67, 'fi', 'Finlande', 0),
(68, 'fj', 'Fiji', 0),
(69, 'fk', 'Falkland (Malouines) iles', 0),
(70, 'fm', 'Micronésie', 0),
(71, 'fo', 'Faroe (iles)', 0),
(72, 'fr', 'France', 0),
(73, 'ga', 'Gabon', 0),
(74, 'gd', 'Grenade', 0),
(75, 'ge', 'Géorgie', 0),
(76, 'gf', 'Guyane Française', 0),
(77, 'gg', 'Guernsey', 0),
(78, 'gh', 'Ghana', 0),
(79, 'gi', 'Gibraltar', 0),
(80, 'gl', 'Groenland', 0),
(81, 'gm', 'Gambie', 0),
(82, 'gn', 'Guinée', 0),
(83, 'gp', 'Guadeloupe', 0),
(84, 'gq', 'Guinée Equatoriale', 0),
(85, 'gr', 'Grèce', 0),
(86, 'gs', 'Georgie du sud et iles Sandwich du sud', 0),
(87, 'gt', 'Guatemala', 0),
(88, 'gu', 'Guam', 0),
(89, 'gw', 'Guinée-Bissau', 0),
(90, 'gy', 'Guyana', 0),
(91, 'hk', 'Hong Kong', 0),
(92, 'hm', 'Heard et McDonald (iles)', 0),
(93, 'hn', 'Honduras', 0),
(94, 'hr', 'Croatie', 0),
(95, 'ht', 'Haiti', 0),
(96, 'hu', 'Hongrie', 0),
(97, 'id', 'Indonésie', 0),
(98, 'ie', 'Irlande', 0),
(99, 'il', 'Israël', 0),
(100, 'im', 'Ile de Man', 0),
(101, 'in', 'Inde', 0),
(102, 'io', 'Territoire Britannique de l\'Océan Indien', 0),
(103, 'iq', 'Iraq', 0),
(104, 'ir', 'Iran', 0),
(105, 'is', 'Islande', 0),
(106, 'it', 'Italie', 0),
(107, 'je', 'Jersey', 0),
(108, 'jm', 'Jamaïque', 0),
(109, 'jo', 'Jordanie', 0),
(110, 'jp', 'Japon', 0),
(111, 'ke', 'Kenya', 0),
(112, 'kg', 'Kirgizstan', 0),
(113, 'kh', 'Cambodge', 0),
(114, 'ki', 'Kiribati', 0),
(115, 'km', 'Comores', 0),
(116, 'kn', 'Saint Kitts et Nevis', 0),
(117, 'kp', 'Corée du nord', 0),
(118, 'kr', 'Corée du sud', 0),
(119, 'kw', 'Koweït', 0),
(120, 'ky', 'Caïmanes (iles)', 0),
(121, 'kz', 'Kazakhstan', 0),
(122, 'la', 'Laos', 0),
(123, 'lb', 'Liban', 0),
(124, 'lc', 'Sainte Lucie', 0),
(125, 'li', 'Liechtenstein', 0),
(126, 'lk', 'Sri Lanka', 0),
(127, 'lr', 'Liberia', 0),
(128, 'ls', 'Lesotho', 0),
(129, 'lt', 'Lituanie', 0),
(130, 'lu', 'Luxembourg', 0),
(131, 'lv', 'Latvia', 0),
(132, 'ly', 'Libyan Arab Jamahiriya', 0),
(133, 'ma', 'Maroc', 0),
(134, 'mc', 'Monaco', 0),
(135, 'md', 'Moldavie', 0),
(136, 'mg', 'Madagascar', 0),
(137, 'mh', 'Marshall (iles)', 0),
(138, 'mk', 'Macédoine', 0),
(139, 'ml', 'Mali', 0),
(140, 'mm', 'Myanmar', 0),
(141, 'mn', 'Mongolie', 0),
(142, 'mo', 'Macao', 0),
(143, 'mp', 'Mariannes du nord (iles)', 0),
(144, 'mq', 'Martinique', 0),
(145, 'mr', 'Mauritanie', 0),
(146, 'ms', 'Montserrat', 0),
(147, 'mt', 'Malte', 0),
(148, 'mu', 'Maurice (ile)', 0),
(149, 'mv', 'Maldives', 0),
(150, 'mw', 'Malawi', 0),
(151, 'mx', 'Mexique', 0),
(152, 'my', 'Malaisie', 0),
(153, 'mz', 'Mozambique', 0),
(154, 'na', 'Namibie', 0),
(155, 'nc', 'Nouvelle Calédonie', 0),
(156, 'ne', 'Niger', 0),
(157, 'nf', 'Norfolk (ile)', 0),
(158, 'ng', 'Nigéria', 0),
(159, 'ni', 'Nicaragua', 0),
(160, 'nl', 'Pays Bas', 0),
(161, 'no', 'Norvège', 0),
(162, 'np', 'Népal', 0),
(163, 'nr', 'Nauru', 0),
(164, 'nu', 'Niue', 0),
(165, 'nz', 'Nouvelle Zélande', 0),
(166, 'om', 'Oman', 0),
(167, 'pa', 'Panama', 0),
(168, 'pe', 'Pérou', 0),
(169, 'pf', 'Polynésie Française', 0),
(170, 'pg', 'Papouasie Nouvelle Guinée', 0),
(171, 'ph', 'Philippines', 0),
(172, 'pk', 'Pakistan', 0),
(173, 'pl', 'Pologne', 0),
(174, 'pm', 'St. Pierre et Miquelon', 0),
(175, 'pn', 'Pitcairn (ile)', 0),
(176, 'pr', 'Porto Rico', 0),
(177, 'pt', 'Portugal', 0),
(178, 'pw', 'Palau', 0),
(179, 'py', 'Paraguay', 0),
(180, 'qa', 'Qatar', 0),
(181, 're', 'Réunion (ile de la)', 0),
(182, 'ro', 'Roumanie', 0),
(183, 'ru', 'Russie', 0),
(184, 'rw', 'Rwanda', 0),
(185, 'sa', 'Arabie Saoudite', 0),
(186, 'sb', 'Salomon (iles)', 0),
(187, 'sc', 'Seychelles', 0),
(188, 'sd', 'Soudan', 0),
(189, 'se', 'Suède', 0),
(190, 'sg', 'Singapour', 0),
(191, 'sh', 'St. Hélène', 0),
(192, 'si', 'Slovénie', 0),
(193, 'sj', 'Svalbard et Jan Mayen (iles)', 0),
(194, 'sk', 'Slovaquie', 0),
(195, 'sl', 'Sierra Leone', 0),
(196, 'sm', 'Saint Marin', 0),
(197, 'sn', 'Sénégal', 0),
(198, 'so', 'Somalie', 0),
(199, 'sr', 'Suriname', 0),
(200, 'st', 'Sao Tome et Principe', 0),
(201, 'sv', 'Salvador', 0),
(202, 'sy', 'Syrie', 0),
(203, 'sz', 'Swaziland', 0),
(204, 'tc', 'Turks et Caïques (iles)', 0),
(205, 'td', 'Tchad', 0),
(206, 'tf', 'Territoires Français du sud', 0),
(207, 'tg', 'Togo', 0),
(208, 'th', 'Thailande', 0),
(209, 'tj', 'Tajikistan', 0),
(210, 'tk', 'Tokelau', 0),
(211, 'tm', 'Turkménistan', 0),
(212, 'tn', 'Tunisie', 0),
(213, 'to', 'Tonga', 0),
(214, 'tp', 'Timor Oriental', 0),
(215, 'tr', 'Turquie', 0),
(216, 'tt', 'Trinidad et Tobago', 0),
(217, 'tv', 'Tuvalu', 0),
(218, 'tw', 'Taiwan', 0),
(219, 'tz', 'Tanzanie', 0),
(220, 'ua', 'Ukraine', 0),
(221, 'ug', 'Ouganda', 0),
(222, 'uk', 'Royaume Uni', 0),
(223, 'gb', 'Royaume Uni', 0),
(224, 'um', 'US Minor Outlying (iles)', 0),
(225, 'us', 'Etats Unis', 0),
(226, 'uy', 'Uruguay', 0),
(227, 'uz', 'Ouzbékistan', 0),
(228, 'va', 'Vatican', 0),
(229, 'vc', 'Saint Vincent et les Grenadines', 0),
(230, 've', 'Venezuela', 0),
(231, 'vg', 'Vierges Britaniques (iles)', 0),
(232, 'vi', 'Vierges USA (iles)', 0),
(233, 'vn', 'Viêt Nam', 0),
(234, 'vu', 'Vanuatu', 0),
(235, 'wf', 'Wallis et Futuna (iles)', 0),
(236, 'ws', 'Western Samoa', 0),
(237, 'ye', 'Yemen', 0),
(238, 'yt', 'Mayotte', 0),
(239, 'yu', 'Yugoslavie', 0),
(240, 'za', 'Afrique du Sud', 0),
(241, 'zm', 'Zambie', 0),
(242, 'zr', 'Zaïre', 0),
(243, 'zw', 'Zimbabwe', 0),
(244, 'com', '.COM', 0),
(245, 'net', '.NET', 0),
(246, 'org', '.ORG', 0),
(247, 'edu', 'Education', 0),
(248, 'int', '.INT', 0),
(249, 'arpa', '.ARPA', 0),
(250, 'at', 'Autriche', 0),
(251, 'gov', 'Gouvernement', 0),
(252, 'mil', 'Militaire', 0),
(253, 'su', 'Ex U.R.S.S.', 0),
(254, 'reverse', 'Reverse', 0),
(255, 'biz', 'Businesses', 0),
(256, 'info', '.INFO', 0),
(257, 'name', '.NAME', 0),
(258, 'pro', '.PRO', 0),
(259, 'coop', '.COOP', 0),
(260, 'aero', '.AERO', 0),
(261, 'museum', '.MUSEUM', 0),
(262, 'tv', '.TV', 0),
(263, 'ws', 'Web site', 0),
(264, '--', 'Unknown', 0);

-- --------------------------------------------------------

--
-- Structure de la table `track_c_os`
--

CREATE TABLE IF NOT EXISTS `track_c_os` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `os` varchar(255) NOT NULL DEFAULT '',
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_c_providers`
--

CREATE TABLE IF NOT EXISTS `track_c_providers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` varchar(255) NOT NULL DEFAULT '',
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_c_referers`
--

CREATE TABLE IF NOT EXISTS `track_c_referers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referer` varchar(255) NOT NULL DEFAULT '',
  `counter` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_access`
--

CREATE TABLE IF NOT EXISTS `track_e_access` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_user_id` int(10) UNSIGNED DEFAULT NULL,
  `access_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_cours_code` varchar(40) NOT NULL DEFAULT '',
  `access_tool` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`access_id`),
  KEY `access_user_id` (`access_user_id`),
  KEY `access_cours_code` (`access_cours_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_attempt`
--

CREATE TABLE IF NOT EXISTS `track_e_attempt` (
  `exe_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `question_id` int(11) NOT NULL DEFAULT 0,
  `answer` text NOT NULL,
  `teacher_comment` text NOT NULL,
  `marks` float(6,2) NOT NULL DEFAULT 0.00,
  `course_code` varchar(40) NOT NULL DEFAULT '',
  `position` int(11) DEFAULT 0,
  `tms` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `exe_id` (`exe_id`),
  KEY `user_id` (`user_id`),
  KEY `question_id` (`question_id`),
  KEY `exe_id_2` (`exe_id`),
  KEY `user_id_2` (`user_id`),
  KEY `question_id_2` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_attempt_recording`
--

CREATE TABLE IF NOT EXISTS `track_e_attempt_recording` (
  `exe_id` int(10) UNSIGNED NOT NULL,
  `question_id` int(10) UNSIGNED NOT NULL,
  `marks` int(11) NOT NULL,
  `insert_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `author` int(10) UNSIGNED NOT NULL,
  `teacher_comment` text NOT NULL,
  KEY `exe_id` (`exe_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_course_access`
--

CREATE TABLE IF NOT EXISTS `track_e_course_access` (
  `course_access_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_code` varchar(40) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_course_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `logout_course_date` datetime DEFAULT NULL,
  `counter` int(11) NOT NULL,
  PRIMARY KEY (`course_access_id`),
  KEY `user_id` (`user_id`),
  KEY `login_course_date` (`login_course_date`),
  KEY `course_code` (`course_code`),
  KEY `user_id_2` (`user_id`),
  KEY `login_course_date_2` (`login_course_date`),
  KEY `course_code_2` (`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_default`
--

CREATE TABLE IF NOT EXISTS `track_e_default` (
  `default_id` int(11) NOT NULL AUTO_INCREMENT,
  `default_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `default_cours_code` varchar(40) NOT NULL DEFAULT '',
  `default_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `default_event_type` varchar(20) NOT NULL DEFAULT '',
  `default_value_type` varchar(20) NOT NULL DEFAULT '',
  `default_value` tinytext NOT NULL,
  PRIMARY KEY (`default_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_downloads`
--

CREATE TABLE IF NOT EXISTS `track_e_downloads` (
  `down_id` int(11) NOT NULL AUTO_INCREMENT,
  `down_user_id` int(10) UNSIGNED DEFAULT NULL,
  `down_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `down_cours_id` varchar(40) NOT NULL DEFAULT '',
  `down_doc_path` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`down_id`),
  KEY `down_user_id` (`down_user_id`),
  KEY `down_cours_id` (`down_cours_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_exercices`
--

CREATE TABLE IF NOT EXISTS `track_e_exercices` (
  `exe_id` int(11) NOT NULL AUTO_INCREMENT,
  `exe_user_id` int(10) UNSIGNED DEFAULT NULL,
  `exe_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `exe_cours_id` varchar(40) NOT NULL DEFAULT '',
  `exe_exo_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `exe_result` float(6,2) NOT NULL DEFAULT 0.00,
  `exe_weighting` float(6,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT '',
  `data_tracking` text NOT NULL,
  `steps_counter` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `session_id` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `orig_lp_id` int(11) NOT NULL DEFAULT 0,
  `orig_lp_item_id` int(11) NOT NULL DEFAULT 0,
  `exe_duration` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `expired_time_control` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`exe_id`),
  KEY `exe_user_id` (`exe_user_id`),
  KEY `exe_cours_id` (`exe_cours_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_hotpotatoes`
--

CREATE TABLE IF NOT EXISTS `track_e_hotpotatoes` (
  `exe_name` varchar(255) NOT NULL,
  `exe_user_id` int(10) UNSIGNED DEFAULT NULL,
  `exe_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `exe_cours_id` varchar(40) NOT NULL,
  `exe_result` smallint(6) NOT NULL DEFAULT 0,
  `exe_weighting` smallint(6) NOT NULL DEFAULT 0,
  KEY `exe_user_id` (`exe_user_id`),
  KEY `exe_cours_id` (`exe_cours_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_hotspot`
--

CREATE TABLE IF NOT EXISTS `track_e_hotspot` (
  `hotspot_id` int(11) NOT NULL AUTO_INCREMENT,
  `hotspot_user_id` int(11) NOT NULL,
  `hotspot_course_code` varchar(50) NOT NULL,
  `hotspot_exe_id` int(11) NOT NULL,
  `hotspot_question_id` int(11) NOT NULL,
  `hotspot_answer_id` int(11) NOT NULL,
  `hotspot_correct` tinyint(3) UNSIGNED NOT NULL,
  `hotspot_coordinate` text NOT NULL,
  PRIMARY KEY (`hotspot_id`),
  KEY `hotspot_course_code` (`hotspot_course_code`),
  KEY `hotspot_user_id` (`hotspot_user_id`),
  KEY `hotspot_exe_id` (`hotspot_exe_id`),
  KEY `hotspot_question_id` (`hotspot_question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_lastaccess`
--

CREATE TABLE IF NOT EXISTS `track_e_lastaccess` (
  `access_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `access_user_id` int(10) UNSIGNED DEFAULT NULL,
  `access_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_cours_code` varchar(40) NOT NULL,
  `access_tool` varchar(30) DEFAULT NULL,
  `access_session_id` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`access_id`),
  KEY `access_user_id` (`access_user_id`),
  KEY `access_cours_code` (`access_cours_code`),
  KEY `access_session_id` (`access_session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_links`
--

CREATE TABLE IF NOT EXISTS `track_e_links` (
  `links_id` int(11) NOT NULL AUTO_INCREMENT,
  `links_user_id` int(10) UNSIGNED DEFAULT NULL,
  `links_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `links_cours_id` varchar(40) NOT NULL DEFAULT '',
  `links_link_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`links_id`),
  KEY `links_cours_id` (`links_cours_id`),
  KEY `links_user_id` (`links_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_login`
--

CREATE TABLE IF NOT EXISTS `track_e_login` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `login_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `login_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_ip` varchar(39) NOT NULL DEFAULT '',
  `logout_date` datetime DEFAULT NULL,
  PRIMARY KEY (`login_id`),
  KEY `login_user_id` (`login_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_online`
--

CREATE TABLE IF NOT EXISTS `track_e_online` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `login_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `login_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `login_ip` varchar(39) NOT NULL DEFAULT '',
  `course` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`login_id`),
  KEY `login_user_id` (`login_user_id`),
  KEY `course` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_open`
--

CREATE TABLE IF NOT EXISTS `track_e_open` (
  `open_id` int(11) NOT NULL AUTO_INCREMENT,
  `open_remote_host` tinytext NOT NULL,
  `open_agent` tinytext NOT NULL,
  `open_referer` tinytext NOT NULL,
  `open_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`open_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `track_e_uploads`
--

CREATE TABLE IF NOT EXISTS `track_e_uploads` (
  `upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_user_id` int(10) UNSIGNED DEFAULT NULL,
  `upload_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upload_cours_id` varchar(40) NOT NULL DEFAULT '',
  `upload_work_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`upload_id`),
  KEY `upload_user_id` (`upload_user_id`),
  KEY `upload_cours_id` (`upload_cours_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transacash`
--

CREATE TABLE IF NOT EXISTS `transacash` (
  `transacash_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transacash_empr_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `transacash_desk_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `transacash_user_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `transacash_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `transacash_sold` float NOT NULL DEFAULT 0,
  `transacash_collected` float NOT NULL DEFAULT 0,
  `transacash_rendering` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`transacash_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id_transaction` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `compte_id` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_name` varchar(255) NOT NULL DEFAULT '',
  `machine` varchar(255) NOT NULL DEFAULT '',
  `date_enrgt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_prevue` date DEFAULT NULL,
  `date_effective` date DEFAULT NULL,
  `montant` decimal(16,2) NOT NULL DEFAULT 0.00,
  `sens` int(1) NOT NULL DEFAULT 0,
  `realisee` int(1) NOT NULL DEFAULT 0,
  `commentaire` text DEFAULT NULL,
  `encaissement` int(1) NOT NULL DEFAULT 0,
  `transactype_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `cashdesk_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `transacash_num` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `transaction_payment_method_num` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transaction`),
  KEY `i_realisee` (`realisee`),
  KEY `i_compte_id` (`compte_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transaction_payment_methods`
--

CREATE TABLE IF NOT EXISTS `transaction_payment_methods` (
  `transaction_payment_method_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_payment_method_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`transaction_payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transactype`
--

CREATE TABLE IF NOT EXISTS `transactype` (
  `transactype_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transactype_name` varchar(255) NOT NULL DEFAULT '',
  `transactype_quick_allowed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `transactype_unit_price` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`transactype_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transferts`
--

CREATE TABLE IF NOT EXISTS `transferts` (
  `id_transfert` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_notice` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `num_bulletin` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `date_creation` date NOT NULL,
  `type_transfert` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `etat_transfert` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `origine` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `origine_comp` varchar(255) NOT NULL DEFAULT '',
  `source` smallint(5) UNSIGNED DEFAULT NULL,
  `destinations` varchar(255) DEFAULT NULL,
  `date_retour` date DEFAULT NULL,
  `motif` varchar(255) NOT NULL DEFAULT '',
  `transfert_ask_user_num` int(11) NOT NULL DEFAULT 0,
  `transfert_send_user_num` int(11) NOT NULL DEFAULT 0,
  `transfert_ask_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_transfert`),
  KEY `etat_transfert` (`etat_transfert`),
  KEY `i_etat_transfert_origine` (`etat_transfert`,`origine`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transferts_demande`
--

CREATE TABLE IF NOT EXISTS `transferts_demande` (
  `id_transfert_demande` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `num_transfert` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `date_creation` date NOT NULL,
  `sens_transfert` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `num_location_source` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_location_dest` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `num_expl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `etat_demande` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `date_visualisee` date DEFAULT NULL,
  `date_envoyee` date DEFAULT NULL,
  `date_reception` date DEFAULT NULL,
  `motif_refus` varchar(255) NOT NULL DEFAULT '',
  `statut_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `section_origine` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `resa_trans` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `resa_arc_trans` int(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transfert_demande`),
  KEY `num_transfert` (`num_transfert`),
  KEY `num_location_source` (`num_location_source`),
  KEY `num_location_dest` (`num_location_dest`),
  KEY `num_expl` (`num_expl`),
  KEY `i_resa_trans` (`resa_trans`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transferts_source`
--

CREATE TABLE IF NOT EXISTS `transferts_source` (
  `trans_source_numexpl` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `trans_source_numloc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`trans_source_numexpl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `translation`
--

CREATE TABLE IF NOT EXISTS `translation` (
  `trans_table` varchar(100) NOT NULL DEFAULT '',
  `trans_field` varchar(100) NOT NULL DEFAULT '',
  `trans_lang` varchar(5) NOT NULL DEFAULT '',
  `trans_num` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `trans_small_text` varchar(255) DEFAULT NULL,
  `trans_text` text DEFAULT NULL,
  PRIMARY KEY (`trans_table`,`trans_field`,`trans_lang`,`trans_num`),
  KEY `i_lang` (`trans_lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_absences`
--

CREATE TABLE IF NOT EXISTS `tria_absences` (
  `elev_id` int(11) NOT NULL,
  `date_ab` date NOT NULL,
  `date_saisie` date NOT NULL,
  `duree_ab` double NOT NULL,
  `origin_saisie` varchar(30) DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `motif` text DEFAULT NULL,
  `duree_heure` decimal(10,2) DEFAULT NULL,
  `id_matiere` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `justifier` int(11) DEFAULT NULL,
  `heuredabsence` time DEFAULT NULL,
  `heure_saisie` time DEFAULT NULL,
  `idprof` int(11) DEFAULT NULL,
  `creneaux` varchar(45) DEFAULT NULL,
  `smsenvoye` tinyint(4) NOT NULL DEFAULT 0,
  `courrierenvoyer` tinyint(4) NOT NULL DEFAULT 0,
  `idrattrapage` varchar(250) NOT NULL DEFAULT '',
  KEY `elev_id` (`elev_id`,`date_ab`,`date_fin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_absences_sconet`
--

CREATE TABLE IF NOT EXISTS `tria_absences_sconet` (
  `ideleve` int(11) NOT NULL,
  `nb_abs` int(11) NOT NULL,
  `nb_abs_no_just` int(11) NOT NULL,
  `nb_rtd` int(11) NOT NULL,
  `trimestre` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_absrtdrattrapage`
--

CREATE TABLE IF NOT EXISTS `tria_absrtdrattrapage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `heure_depart` time NOT NULL,
  `duree` time NOT NULL,
  `ref_id_absrtd` varchar(250) NOT NULL DEFAULT '',
  `valider` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_abs_rtd_aucun`
--

CREATE TABLE IF NOT EXISTS `tria_abs_rtd_aucun` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classe` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `heure` time DEFAULT NULL,
  `matiere` varchar(50) NOT NULL,
  `enseignant` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_abs_rtd_info`
--

CREATE TABLE IF NOT EXISTS `tria_abs_rtd_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classe` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `heure` time DEFAULT NULL,
  `matiere` varchar(50) NOT NULL,
  `enseignant` varchar(80) NOT NULL,
  `nbabs` int(11) NOT NULL,
  `nbrtd` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tria_affectations`
--

CREATE TABLE IF NOT EXISTS `tria_affectations` (
  `ordre_affichage` smallint(6) NOT NULL,
  `code_matiere` smallint(6) NOT NULL,
  `code_prof` int(11) NOT NULL,
  `code_classe` smallint(6) NOT NULL,
  `coef` decimal(30,2) NOT NULL,
  `code_groupe` int(11) DEFAULT NULL,
  `langue` varchar(20) DEFAULT NULL,
  `avec_sous_matiere` tinyint(1) NOT NULL,
  `visubull` tinyint(4) NOT NULL DEFAULT 1,
  `nb_heure` varchar(30) NOT NULL DEFAULT ' ',
  `trim` varchar(30) NOT NULL DEFAULT 'tous',
  `ects` varchar(10) NOT NULL DEFAULT '0',
  `id_ue_detail` int(11) NOT NULL,
  `specif_etat` varchar(50) NOT NULL,
  `annee_scolaire` varchar(15) NOT NULL,
  `visubullbtsblanc` tinyint(4) NOT NULL DEFAULT 0,
  `num_semestre_info` int(11) NOT NULL,
  `coef_certif` varchar(10) NOT NULL,
  `note_planche` varchar(15) NOT NULL,
  PRIMARY KEY (`code_classe`,`code_matiere`,`code_prof`,`ordre_affichage`,`trim`,`annee_scolaire`),
  KEY `code_classe` (`code_classe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

COMMIT;


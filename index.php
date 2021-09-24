<?php

error_reporting(E_ALL);

mb_internal_encoding("UTF-8");

$post = null;

if (!empty($_POST))
{
	$post = file_get_contents('php://input');
	
	
	$text = $_POST['text'];
	echo $text;
}
else
{
?>

<html>
<head>
	<style>
		body {
			padding:20px;
			font-family:sans-serif;
		}
	</style>
</head>
<body>
	<h1>Glasgow Geoparser</h1>
	<form action="." method="post">
		<textarea id="text" name="text" rows="10" cols="80">Species of Symphurus (Pleuronectiformes: Cynoglossidae) are relatively small-sized tonguefishes occurring worldwide in tropical, subtropical, and warm-temperate seas. In the Indo-West Pacific Ocean, species of Symphurus inhabiting waters shallower than 200 m are rarely reported; only five have been described, S. microrhynchus (Weber, 1913), S. holothuriae Chabanaud, 1948, S. monostigmus Munroe, 2006, S. leucochilus Lee et al. 2014, and S. longirostris Lee et al. 2016. Examination of museum and recently collected specimens yielded over 100+ Symphurus captured in relatively shallow waters off Japan, Papua New Guinea, the Philippines, Taiwan, and Vietnam. All of these specimens, except S. monostigmus (with 14 caudal-fin rays), were originally tentatively identified as S. microrhynchus because of shared similarities (small size, low meristic values, 12 caudal-fin rays, shared pigmentation traits). Detailed comparisons revealed that, although similar, specimens from allopatric locations have small differences in meristic, morphometric and pigmentation features. In previous literature, these small differences were thought to represent intraspecific variation among populations of a widespread species, S. microrhynchus. However, further study, including molecular data, has revealed that such minor differences among specimens from allopatric locations actually represent interspecific, and not population-level, variations. Where available, molecular differences among these allopatric populations, in contrast to the morphological features, were significantly different (9.0 to 26.3%), providing additional strong support for the hypothesis that more than one species is represented among fishes examined. Combined data from morphological and molecular characters, and species delimitation analysis, reveal that five, undescribed, cryptic species should be recognized: S. brachycephalus n. sp. from Vietnam, S. hongae n. sp. from Taiwan, S. leptosomus n. sp. from the Philippines, S. polylepis n. sp. from Papua New Guinea, and S. robustus n. sp. from Japan. Also, based on new information, the previous decision to place S. holothuriae Chabanaud in the synonymy of S. microrhynchus was determined to be premature. This species should be recognized as valid until additional specimens are captured and the taxonomic status of this nominal species re-evaluated. At least 10 species of Indo-West Pacific shallow-water Symphurus are now known. Eight are members of the Symphurus microrhynchus species complex with hypothesized closer relationship to each other than to the other two species of shallow-water tonguefishes. Included in this study are redescriptions of S. microrhynchus and S. holothuriae based on their holotypes, including an expanded number of morphological characters not previously used to diagnose these species; redescriptions are also provided for comparative purposes of three other shallow-water species; five new cryptic species are described; and lastly, detailed comparisons and an identification key to all 10 species of shallow-water Symphurus occurring in the Indo-West Pacific Ocean are provided. Two additional populations are also identified that likely represent other undescribed taxa belonging to the S. microrhynchus species complex. Adequate specimens are not available at this time to formally describe these nominal species. This study contributes further understanding about species diversity within Symphurus inhabiting shallow waters of the Indo-West Pacific Ocean. Several other nominal species of small-sized cynoglossid and soleid flatfishes are currently considered to have widespread distributions in the Indo-West Pacific. Many of these species also have junior synonyms available based on nominal species described from allopatric sites within their geographic ranges. How many of these presumed populations of widespread species will be resurrected from synonymy once additional specimens and their genetic information becomes available remains an interesting question for future study.		
		</textarea>
		<br/>
		<input type = "submit" name = "submit" value = "Parse">
	</form>
</body>
</html>


<?php
}
?>

# Glasgow Geoparser

A simple tool to [geoparse](https://en.wikipedia.org/wiki/Toponym_resolution) using a gazetteer derived from Wikidata, combined with FlashText search. 

Typically geoparsing involves taking a body of text and undertaking the following two steps:
- using [Named-entity recognition (NER)](https://en.wikipedia.org/wiki/Named-entity_recognition) to identity named entities in the text (e.g., place names, people names, etc.)
- using a gazetteer of geographic names (e.g., [GeoNames](http://www.geonames.org) try and match the place names found by NER.

An example of such a parser is the [Edinburgh Geoparser](https://www.ltg.ed.ac.uk/software/geoparser/) (which the name “Glasgow Parser” is a play on). Typically geoparsing software can be large and tricky to install, especially if you are looking to make your installation publicly accessible. Geoparsing services seem to have a short half-life (e.g., [Geoparser.io](https://geoparser.io), perhaps because they are so useful they quickly get swamped by users.

Bearing this in mind, the approach I’ve taken here is to create a very simple geoparser that is focussed on fairly large areas, especially those relevant to biodiversity, and is aimed at geoparsing text such as abstracts of scientific papers. It is not intended to be used to parse locality data for specimens, for example. For that level of granularity I think GBIF is probably the best gazetteer we have (see https://lyrical-money.glitch.me ).

To create the Glasgow Geoparser I fetch localities from Wikidata and create a CSV file with basic data including names and geographic coordinates, along the lines of [wikidata-gazetteer
Public](https://github.com/Wikidata-Gazetteer/wikidata-gazetteer) but with fewer terms, and with a much smaller subset of Wikidata. Once this data is assembled I parse it and create a  trie. This makes it easy to quickly test for any string whether it occurs in the dataset. I use the FlashText algorithm to parse a block of text and extract all string that match names in the dataset. 


## Wikidata

I use a series of SPARQL queries to generate CSV files of localities that I consider to be most likely to appear in the abstracts of articles relevant to biodiversity (e.g., taxonomic papers). The data fields are:

key | value
-- | --
wikidata_id | Wikidata id (QID)
name | English name
enwiki_title | title of English Wikipedia article
alternate_names | names in other languages, synonyms
country_code | ISO country code (e.g., FR)
latitude | latitude in decimal degrees
longitude | longitude in decimal degrees
geonames_id | id in GeoNames
osm_id | id in OpenStreetMap

For some queries Wikidata times out, so an alternative is to use two queries. The first finds a set of ids that match a query (e.g., islands > 20,000 km<sup>2</sup> in area) then run those ids through `idquery.php` which chunks the set of ids into smaller pieces and runs a query for that set of ids. In other words, we are simply asking for properties of a known id. This approach also enable us to have a list of localities that might not match a simple query (e.g., countries) but which is of interest.


## FlashText

The algorithm for locating geographic names in text uses a [trie](https://en.wikipedia.org/wiki/Trie) and is described in a paper by [Vikash Singh](https://github.com/vi3k6i5).

> Singh, V. (2017). Replace or Retrieve Keywords In Documents at Scale. CoRR, abs/1711.00046. http://arxiv.org/abs/1711.00046

To implement this algorithm I used [Trie tree (prefix tree) detailed-PHP code implementation](https://www.programmerall.com/article/4530755185/) as a starting point, then modified it following the Python code given in the article by Vikash Singh. Typically in a trie there is a “stop” character to indicate the end of a word. In this case we also have a pointer to a data object, namely the data from Wikidata. Hence if we find a term in the trie we can instantly retrieve the corresponding data.

## Output

The output of the algorithm is a list of all the strings in the text that match geographic names in the gazetteer. This can be easily converted into GeoJSON for display.


## Build database

Datasets are created in `Wikidata` folder. Run `wikidata-to-trie.php` in root folder to parse csv files into trie structure.


## Queries


### Countries

### ADM1 areas

### Large islands



SELECT * WHERE {
  ?item wdt:P31 wd:Q23442.
  
  ?item wdt:P2046 ?area .
  #FILTER(?area > 100000) .
  
  
}
LIMIT 10




## Examples to work through


https://www.biodiversitylibrary.org/part/270384

https://www.biodiversitylibrary.org/page/56235889

Note that we need to remove line breaks, and also we get hits in African and New Guinea(!)

> Richards, S.J., Oliver, P., Brown, R.M.: A new scansorial species of Platymantis (Anura: Ceratobatrachidae)...  (plate 1)  Guinea, New Britain Island, East New Britain  Province, Vouvou Camp: SAMA R64805.  Platymantis caesiops Kraus, Allison, 2009  Material examined: 2 specimens, Papua New  Guinea, New Britain Island, East New Britain  Province, Vouvou Camp: SAMA R10730, 10732.  Platymantis cheesmanae Parker, 1940  Material examined: 3 specimens, Indonesia,  Cyclops Mountains, Wambena Camp: SJR 6212,  6201, 6204.  Platymantis citrinospilus Brown, Richards,  Broadhead, 2013  Material examined: 4 specimens, Papua New  Guinea, New Britain Island, East New Britain  Province, Nakanai Mountains, Tompoi Camp, 1700  m above sea level: SAMA R64758 (holotype), SAMA  R64756, R64757, PNGNM 24042 (paratypes).  Platymantis desticans Brown, Richards, 2008  Material examined: 4 specimens, Solomon  Islands, Isabel Province, Barora Faa Island, (off  the western tip of Isabel Island): SAMA R56849  (holotype), and SAMA R56850-52 (paratypes).  Platymantis gillardi Zweifel, 1960  Material examined: 17 specimens, Papua New  Guinea, Bismarck Archipelago, New Britain Island,  West New Britain Province, S coast, ca 7 mi NW  Pomugu, Kandrian: CAS-SU 22877-78; Papua  New Guinea, West New Britain Province, northern  Nakanai Mountains, ridge between the Ivule and  Sigole rivers on the northern edge of the Nakanai  Plateau: UWZM 23787-96, 23799-800; East New  Britain Province, Vouvou Camp: SAMA R64801-02.  Platymantis guppyi (Boulenger, 1884)  Material examined: 59 specimens, Papua New  Guinea, Bougainville Island, Bougainville Province, Camp Torokina: USNM 120852-53; Kunua: MCZ-A 38628, 38632-33, 38635, 38638-39, 38664-  666, 38668, 38674, KU 93736-40, 98159-65,  98468; Melilup: MCZ-A 38629, 38659-60, 38667,  38669-72, 59498-501; Mutahi: CAS 106553-  106565; Solomon Islands, Barora Faa Island (near  Isabel Island): SAMA R56839, 56840; Guadalcanal Island, Tadai District, Mt. Austen, Barana Village:  KU 307359, 307375-76, 307381, 307384-86.  Platymantis latro Richards, Mack, Austin, 2007  Material examined: 18 specimens, Papua New  Guinea, Admiralty Islands, Manus Province, Manus Island: KU 93750-54; Chachuau Camp near Tulu  1 Village: SAMA R62819 (holotype), UPNG 10051,  SAMA R62820; Natnewai Camp: SAMA R62826;  Lorengau: UPNG 10052-54, SAMA R62821-23;  Rambutyo Island, Penchal Village: SAMA R62827;  Los Negros Island, Salami Village: SAMA R62828-  29 (paratypes).  Platymantis macrops (Brown, 1965)  Material examined: 4 specimens, Solomon  Islands, North Solomons, Bougainville Island,  Bougainville Province, Kunua: MCZ-A 38195-96  (paratypes); Aresi, S. of Kunua: MCZ-A 41864  (holotype); Matsiogu: MCZ-A 78820.  Platymantis macrosceles Zweifel, 1975  Material examined: 4 specimens, Papua New  Guinea, West New Britain Province, Ti, Nakanai  Mountains (central New Britain): BPBM 1005  (holotype); Nakanai Mountains, ridge between  the Ivule and Sigole Rivers: UWZM 23721, UPNG  10007; Papua New Guinea, East New Britain  Province, Vouvou Camp: SAMA R64815.  Platymantis magnus Brown, Menzies, 1979  Material examined: 4 specimens, Papua New  Guinea, New Ireland Island, New Ireland Province,  W. Coast, approx. 88 km S Kavieng (“Madina  High School area”): CAS 143640, (holotype); CAS  143639 (paratype); Utu, 1 km S, 5 km E Kavieng:  MCZ-A 92671-72 (paratypes).  Platymantis mamusiorum Foufopoulos, Brown,  2004  Material examined: 2 specimens, Papua New  Guinea, West New Britain Province, northern  Nakanai Mountains, ridge between the Ivule and  Sigole rivers on the northern edge of the Nakanai  Plateau (05°33.112’S, 151°04.269’E): UWZM  23720 (holotype), UWZM 23719, 23722, UPNG  9992 (Paratypes); Papua New Guinea, East New  Britain Province, Vouvou Camp: SAMA R64713-14.  Platymantis man us Kraus, Allison, 2009  Material examined: 2 specimens, Papua New  Guinea, Admiralty Islands, Manus Province, Manus Island, lorengau, MCZ-A 87512 (holotype), 87513  (paratopotype)  Platymantis mimicus Brown, Tyler, 1968  Material examined: 6 specimens, Papua New  Guinea, Bismarck Archipelago, New Britain Island,  West New Britain Province, ca 18 mi S of Talasea,  Numundo Plantation on Willaumez Peninsula: CAS-
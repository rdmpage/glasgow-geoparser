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


## FlashText

The algorithm for locating geographic names in text uses a [trie](https://en.wikipedia.org/wiki/Trie) and is described in a paper by [Vikash Singh](https://github.com/vi3k6i5).

> Singh, V. (2017). Replace or Retrieve Keywords In Documents at Scale. CoRR, abs/1711.00046. http://arxiv.org/abs/1711.00046

To implement this algorithm I used [Trie tree (prefix tree) detailed-PHP code implementation](https://www.programmerall.com/article/4530755185/) as a starting point, then modified it following the Python code given in the article by Vikash Singh. Typically in a trie there is a “stop” character to indicate the end of a word. In this case we also have a pointer to a data object, namely the data from Wikidata. Hence if we find a term in the trie we can instantly retrieve the corresponding data.

## Output

The output of the algorithm is a list of all the strings in the text that match geographic names in the gazetteer. This can be easily converted into GeoJSON for display.


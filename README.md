# Scraper

A simple PHP script that fetches and returns the latest earthquake list from [seismos.gr](https://www.seismos.gr/seismoi-lista) in **JSON** format.



## Description

This script:
- Connects to `https://www.seismos.gr/seismoi-lista`
- Parses the HTML using `DOMDocument` and `DOMXPath`
- Extracts:
  - Earthquake magnitude and level (`magLevel`)
  - Date & time
  - Location
  - Relative time (e.g., "5 minutes ago")
  - Detail page URL
- Returns the data in JSON format with proper UTF-8.
## Requirements

- PHP 7.4 or newer
- **libxml** extension enabled
- Internet access from the server
- `mbstring` extension enabled

## Installation

Clone the repository:

```bash
git clone https://github.com/<username>/<repository>.git
cd <repository>
```



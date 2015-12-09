Behaviours
========
[![Build Status](https://travis-ci.org/milkyway-multimedia/ss-behaviours.png?branch=master)](https://travis-ci.org/milkyway-multimedia/ss-behaviours)

**Behaviours** is a set of common DataExtensions and Traits to use with Silverstripe

* Hashable: This creates a unique hash for your DataObject. This cannot be decoded, but does not rely on any field on the original DataObject. This is used mainly as a replacement for ID if you want to hide the ID from the front-end, or in the case of not needing a unique value it can be a reference to differentiate records by certain attributes (such as product options - same size, colour, etc)
* Sluggable: This creates a slug for the DataObject. This can be decoded back to its original state. Usually used as a replacement for ID with URLs.

## Install
Add the following to your composer.json file
```

    "require"          : {
		"milkyway-multimedia/ss-behaviors": "dev-master"
	},

```

Add the following in your YAML config for objects you would like to hash/slug
```

YourDataObject:
  extensions:
    - Milkyway\SS\Behaviours\Extensions\Hashable

```

## License 
* MIT

## Version 
* 0.2 (Alpha)

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")
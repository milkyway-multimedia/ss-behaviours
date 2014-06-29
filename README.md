Hashable
======

## Still in heavy development
This may not work the way I want it yet, so the API will be changing frequently until I am happy with it. Please use with caution if you must use it at all...

**Hashable** is a DataExtension for quickly adding unique hash capabilities to any data object

By default it will hash/slug an ID

## Install
Add the following to your composer.json file
```

    "require"          : {
		"milkyway-multimedia/hashable": "dev-master"
	},

```

Add the following in your YAML config for objects you would like to hash/slug
```

DataObject_ClassName:
  extensions:
    - Hashable/Sluggable

```

## Extensions
There are two extensions that come with this module, and are slightly different in their use cases

* Hashable: This creates a unique hash for your DataObject. This cannot be decoded, but does not rely on any field on the original DataObject. This is used mainly as a replacement for ID if you want to hide the ID from the front-end, or in the case of not needing a unique value it can be a reference to differentiate records by certain attributes (such as product options - same size, colour, etc)
* Sluggable: This creates a slug for the DataObject. This can be decoded back to its original state. Usually used as a replacement for ID with URLs.

## License 
* MIT

## Version 
* Version 0.1

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")
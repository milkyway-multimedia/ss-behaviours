Hashable
======
**Hashable** is a DataExtension for quickly adding unique hash capabilities to any data object

By default it will hash an ID

## Install
Add the following to your composer.json file
```

    "require"          : {
		"milkyway-multimedia/hashable": "dev-master"
	},

```

Add the following in your YAML config for objects you would like to hash
```

DataObject_ClassName:
  extensions:
    - Hashable

```

## License 
* MIT

## Version 
* Version 0.1

## Contact
#### Milkyway Multimedia
* Homepage: http://milkywaymultimedia.com.au
* E-mail: mell@milkywaymultimedia.com.au
* Twitter: [@mwmdesign](https://twitter.com/mwmdesign "mwmdesign on twitter")
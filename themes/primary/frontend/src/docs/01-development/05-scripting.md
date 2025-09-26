---
title: Scripting
---

# Overview

Scripts are broken down into categories:

| Category | Purpose                                                                       |
| -------- | ----------------------------------------------------------------------------- |
| Vendors  | 3rd Party includes, like modernizr, etc                                       |
| Plugins  | 3rd Party plugins, like Formstone, etc                                        |
| Modules  | Custom modules created by Fastspot                                            |
| Mixins   | Plugin-n-Play bits of code that extract out common functionality. Events, etc |
| Helpers  | Functions / configurations that assist overall development                    |

## Webpack Implementation

Webpack allows us to have some unique features:

### Resolutions

We have set up thev following configuration:

```javascript
resolve: {
	// ..
	modules: ['node_modules'];
}
```

This means when using the `import` syntax, `webpack` will check in `node_modules` first for absolute imports, then locally.

```javascript
// will check node_modules/formstone first
import 'formstone/src/js/core';
```

### Aliases

```javascript
resolve: {
	// ..
	alias: {
		'@': `${srcPath}/js`,
		'@base': basePath
	}
}
```

Aliases help with keep module resolutions relative to a specific directory. This avoid having to use relative pathing everywhere based on the file's location.

```javascript
/**
 * The `@` symbol will resolve to `src/js`
 * no matter where you are in the file tree.
 */
import { accordion } from '@/modules/accordion';
```

### Externals

Externals allow webpack to _ignore_ specific imports if they are included elsewhere, like a `<script>` tag.

Why do this? Imports tell a story of a file's dependencies, so right away you know a file is depending on `jQuery`.

```javascript
externals: {
	jquery: 'jQuery';
}
```

```javascript
/**
 * this will not produce an error even though `jquery`
 * is not in our node_modules folder
 */
import $ from 'jquery';
```

## Import / Export syntax

### Exports

#### Named Exports

```javascript
// file-a.js

export const config = {
	hello: 'world',
};

export const helper = function () {
	//
};

// OR

const config = {
	hello: 'world',
};

const helper = function () {
	//
};

export { config, helper };

// file-b.js

// single
import { config } from 'file-a';
// multiple
import { config, helper } from 'file-a';
// all; allExports.config, allExports.helper, etc
import * as allExports from 'file-a';
```

#### Default Exports

```javascript
// file-a.js

export default function helper() {
	//
}

// OR

const helper = function () {
	//
};

export default helper;

// file-b.js

// you decide what the default export is assigned to
import helper from 'file-a';
// you can name it anything
import blah from 'file-a';
// if the file has named and default exports
import blah, { someNamedExport } from 'file-a';
```

## Adding a new Module

1. Copy the sample module `src/js/modules/sample-module.js` and add it to the same folder
2. Update the following:
   - `NAMESPACE` - this is the key for getting configuration from data attributes.
   - `DEFAULT_SELECTOR`
   - `DEFAULT_SETTINGS`
   - `class name` (currently `SampleModule`)
   - `class name` in the `factory` function (`new SampleModule()`)
3. Add functionality to your module

#### Auto Loading

If you would like your module to automatically be instantiated:

1. Open `src/js/modules/index.js`
2. Add a new line exporting your module
3. Confirm your module now auto loads

#### Manual Loading

```javascript
import sampleModule from '@/modules/sample-module';

sampleModule('.js-sample-module', {
	someSetting: 'value',
});
```

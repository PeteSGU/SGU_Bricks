---
title: Guide
---

## Prefixes

Each type of component has a prefix to avoid naming conflicts.

The configuration for each `collection` can be found in the root folder of the component type.

Example: `twig/components/components.config.js`

More about [collections][fractal-collections].

| Name       | Prefix       | Example                            |
| ---------- | ------------ | ---------------------------------- |
| Component  | `component`  | `render "@component-accordion"`    |
| Partial    | `partial`    | `render "@partial-logo"`           |
| Navigation | `navigation` | `render "@navigation-breadcrumbs"` |
| Layout     | `layout`     | `render "@layout-header"`          |
| Template   | `template`   | `render "@template-home"`          |

## Configuration Files

> _Configuration files inherit from parent configuration files through a merge_

### File Types

**Root**

This is the top most fallback, providing default context for all component types.

| Key           | Value                 |
| ------------- | --------------------- |
| Inherits from | none                  |
| Example       | `twig/twig.config.js` |

**Collection**

This will inherit from `Root`, and provides default context for all components contained within the same folder

| Key           | Value                                  |
| ------------- | -------------------------------------- |
| Inherits from | `Root`                                 |
| Example       | `twig/components/components.config.js` |

**Component**

This will inherit from both the `Collection` and `Root` configuration, and provides default context at a component level

| Key           | Value                                           |
| ------------- | ----------------------------------------------- |
| Inherits from | `Collection`, `Root`                            |
| Example       | `twig/components/accordion/accordion.config.js` |

## Render vs Include tag

### Render

> The `render` tag will use the component's default context if not provided.

#### When to Use

In most situations, this should only be used when referencing a `component` in a `template` and the context will remain unchanged.

For example, on the home template, you could have:

```twig
<div class="full_width_callouts">
	{{ '{{ render "@component-accordion" }}' }}
	{{ '{{ render "@component-call-to-action" }}' }}
</div>
```

If you ever need to alter the context, the `include` tag should be used instead.

### Include

> the `include` tag will _not_ use the component's default context; it must be provided.

#### When to Use

This should be used in most situations especially for `partials`, as it forces you to to pass in the context manually. This reduces in the confusion from the `render` tag and the way it merges `context`.

For example, here we update the tools keyword, so we used the `include` tag instead:

```twig
{{ '{% include "@partial-filter" with {
	label: "",
	active: "",
	action_category: "#",
	action_search: "#",
	tools: [
		{
			label: "Category",
			options: [
				{
					label: "All Categories"
				},
				{
					label: "Category One",
					selected: true
				},
				{
					label: "Another Category"
				}
			]
		},
		{
			label: "Type",
			options: [
				{
					label: "All Types"
				},
				{
					label: "Type One"
				},
				{
					label: "Another Type"
				}
			]
		}
	],
	search_placeholder: "Search by keyword",
	results: "",
	category: ""
} %} '}}
```

[fractal-collections]: https://fractal.build/api/entities/collection.html#collection

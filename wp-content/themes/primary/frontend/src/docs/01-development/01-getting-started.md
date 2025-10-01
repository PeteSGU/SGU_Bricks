---
title: Getting Started
---

## Setup

```bash
# 1. install dependencies

npm install

# 2. run gulp's default task
# - this will start a local server on port 3000
# - http://localhost:3000

npm run gulp # local
gulp # or global
```

## Available Scripts

> _All scripts are available under the `scripts` key in the `package.json`_

All commands can be run through `npm` per the following example:

```bash
npm run [command name]
```

| Name            | Description                                                                                   |
| --------------- | --------------------------------------------------------------------------------------------- |
| `gulp`          | will run the `default gulp task`;<br> builds `static assets` + start the `fractal web server` |
| `build`         | generates all static assets (scripts, styles, etc) and add to the `dist/` folder              |
| `fractal:build` | generates all static `fractal` files and adds them to the `static-html/` folder               |
| `github:build`  | this is used for the `Github Action`; generates both `static + fractal` assets                |

[github-framework-static]: https://github.com/fastspot/framework-static

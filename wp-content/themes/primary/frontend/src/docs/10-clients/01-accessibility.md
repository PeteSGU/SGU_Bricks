---
title: Accessibility
---

All pages of your site are run through a tool called [Axe][axe]. Axe is testing software that recommends best accessibility practices for pages on your website.

Your site has been run through Axe and has been deemed accessible per accessibility standards.

## What pages do we test?

We test all the pages found in the Templates section of your component library. This ensure every template type on your site meets accessibility standards for your users.

The accessibility test can be found under ["Accessibility > Axe"][page-axe] inside Fractal.

## False Positives

Some `Axe` errors turn out to be false positives for a variety of reasons. For example, text over an image needs to have sufficent contrast which is something that cannot be checked through automation. These sort of violations can be safely ignored after manual testing resulted in accessibility passing.

## Third Party

Some `Axe` errors are because of third party code, for example a YouTube video embed or Google Custom Search.

[page-axe]: {{ "/components/preview/accessibility-axe" | path }}
[axe]: https://www.deque.com/axe/

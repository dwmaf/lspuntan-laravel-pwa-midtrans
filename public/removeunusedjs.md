Remove unused JavaScript



Unused JavaScript can slow down your page load speed:

If the JavaScript is render-blocking, the browser must download, parse, compile, and evaluate the script before it can proceed with all of the other work that's needed for rendering the page.
Even if the JavaScript is asynchronous (not render-blocking), the code competes for bandwidth with other resources while it's downloading, which has significant performance implications. Sending unused code over the network is also wasteful for mobile users who don't have unlimited data plans.
How the unused JavaScript audit fails
Lighthouse flags every JavaScript file with more than 20 kibibytes of unused code:

A screenshot of the audit.
Click a value in the URL column to open the script's source code in a new tab.
Note: See the Lighthouse performance scoring post to learn how your page's overall performance score is calculated.
How to remove unused JavaScript
Detect unused JavaScript
The Coverage tab in Chrome DevTools can give you a line-by-line breakdown of unused code.

The Coverage class in Puppeteer can help you automate the process of detecting unused code and extracting used code.

Build tool for support for removing unused code
Check out the following Tooling.Report tests to find out if your bundler supports features that make it easier to avoid or remove unused code:

Code Splitting
Unused Code Elimination
Unused Imported Code
Stack-specific guidance
Angular
If you are using Angular CLI, include source maps in your production build to inspect your bundles.

Drupal
Consider removing unused JavaScript assets and only attach the needed Drupal libraries to the relevant page or component in a page. See the Drupal documentation for details. To identify attached libraries that are adding extraneous JavaScript, try running code coverage in Chrome DevTools. You can identify the theme or module responsible from the URL of the script when JavaScript aggregation is disabled in your Drupal site. Look out for themes or modules that have many scripts in the list which have a lot of red in code coverage. A theme or module should only attach a script library if it is actually used on the page. for details.

Joomla
Consider reducing, or switching, the number of Joomla extensions loading unused JavaScript in your page.

Magento
Disable Magento's built-in JavaScript bundling.

React
If you are not server-side rendering, split your JavaScript bundles with React.lazy(). Otherwise, code-split using a third-party library such as loadable-components.

Vue
If you are not server-side rendering and using the Vue router, split the bundles by lazy loading routes.

WordPress
Consider reducing, or switching, the number of WordPress plugins loading unused JavaScript in your page.
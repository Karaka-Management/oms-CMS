# Structure

## General

The CMS module consists of the following main content and templating structure:

* Templates: Files which specify the general layout for the different pages
* Pages: Pages are database entries which can contain localized content to be loaded by the respective template pages. In the backend a page automatically shows all available localization elements for a page.
* Page Localizations: Pages can have different localization elements. A page can have multiple page localization elements which allows to specify different content areas on a page. Example:
    * You can for example define the content of a whole page including the HTML structuring in a page which is then loaded by the template file. This reduces the amount of localization elements resulting in faster loading times but requires the user to understand some basic html/css if this is part of the localziation. Or,
    * you can define every content element (e.g. box, card, etc.) on the front page as a separate page localization which then can be loaded individually by the template file. By doing this the user doesn't need to know any html/css because the user can only edit pure text but in return the loading times are slower and changes to other aspects of the page can only get changed by modifying the template files.
* Localization file: Every application can have one localization file which defines general translations for the application/website. This makes it possible to create a localized website for basic elements such as general headlines/sections, links, without creating a localized page.

General advice:

* Define basic localizations which are part of the website template/design in the localziation files and define all/most of the content directly in the template (e.g. contact form)
* Use multiple localizations elements for pages if the content of those elements is changed regularly (e.g. front page elements) otherwise consider to only use one localization element per page (e.g. terms of service)

## ER

![ER](Modules/CMS/Docs/Dev/img/er.png)

# B2bzanden Change Category Title Magento 2 Module

## Installation
To install extensions via composer you need to add our repository to your composer configuration using the following command:

```
composer config repositories.hans2103 composer https://hans2103.nl/satis
```

Now you can install the extension using the following command:
```
composer require b2bzanden/changecategorytitle
```
  
## What does this module do?
This module will append or prepend text to the visible catalog category title depending on the level of the category.

- ' onderdelen' will be prepended on level 2 and 3
- Parent Category Name will be appended on level 4 and 5

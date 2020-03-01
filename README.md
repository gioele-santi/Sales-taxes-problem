# Sales taxes problem

This code is my solution to the *"Sales taxes problem"*. 

The impementation was first tested against provided inputs using hardcoded unit tests. Then the code was converted to work as a command line application that the user can feed with any wanted input.

## How to use

You can download the whole folder and launch the script from within the folder:

```
cd folder_path
```

To execute the program you need to provide at least the paths of two input **.txt** files:
- the actual input with product description, unit count and unit price for each line;
- a list of exempted products (each line must contain the name of a product exempt from taxes).

To run the script use the following command:
```
receipt.php input_3.txt exempt_products.txt
```

Output will have following format:
```
1 imported bottle of perfume: 32.19
1 bottle of perfume: 20.89
1 packet of headache pills: 9.75
3 imported box of chocolates: 35.55
Sales Taxes: 7.90
Total: 98.38
```

### Verbose option 

It is possible to view information about loaded input by adding **-v** as third parameter:
```
receipt.php input_3.txt exempt_products.txt -v
```

will output:

```
Loaded input:

1 imported bottle of perfume at 27.99
1 bottle of perfume at 18.99
1 packet of headache pills at 9.75
3 box of imported chocolates at 11.25


Output:

1 imported bottle of perfume: 32.19
1 bottle of perfume: 20.89
1 packet of headache pills: 9.75
3 imported box of chocolates: 35.55
Sales Taxes: 7.90
Total: 98.38
```
## Automated tests

You can run automated tests by launching this command:
```
php test.php
```

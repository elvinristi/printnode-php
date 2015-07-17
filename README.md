PrintNode-PHP
=============

PrintNode is a cloud printing services which allows you to connect any printer to your application using our PrintNode Client and easy to use JSON API.  

See www.printnode.com for more information.

This quick start guide covers using the PHP API library. It shows how to find which Computers and Printers you have available for printing and how you can submit PrintJobs using the provided PHP API libraries.

## Step 1: Sign Up
Before you can use the API, you will need to sign up to PrintNode account, and make a new API key. To help get you started, you will get 50 free prints when you sign up.

## Step 2: Add a computer and printer
To have somewhere to print to you need to download and install the PrintNode desktop client on a computer with some printers. You can download the PrintNode Client installer here - www.printnode.com/download . It should be intuitive to setup but for more detailed instructions please see here: https://www.printnode.com/docs/installation/windows/ .

## Step 3: Install library

### Download the PHP Library
You can download the client from our Github account. If you have a git client installed locally, you can clone our repository from the Github website. Alternatively, you can also download archives of the files.

### Install via composer

```bash
composer require PrintNode/printnode-php:dev-master
```

## Step 4: See examples how to use this library

See `examples` directory to learn how to use this library.
# Magento 2 Share Cart extension Free

[Mageplaza Share Cart Extension](https://www.mageplaza.com/magento-2-share-cart/) helps customers in sharing their shopping cart with friends and family as well. This is a supportive method to promote store’s conversion rate via the existing users, and this can significantly contribute to the revenue of the store.

- Share shopping cart quickly 
- Shortly review purchasing cart
- Download the PDF file with full information

[![Latest Stable Version](https://poser.pugx.org/mageplaza/module-share-cart/v/stable)](https://packagist.org/packages/mageplaza/module-share-cart)
[![Total Downloads](https://poser.pugx.org/mageplaza/module-share-cart/downloads)](https://packagist.org/packages/mageplaza/module-share-cart)

## 1. Documentation

- [Installation guide](https://www.mageplaza.com/install-magento-2-extension/)
- [User guide](https://docs.mageplaza.com/share-cart/index.html)
- [Introduction page](http://www.mageplaza.com/magento-2-share-cart/)
- [Contribute on Github](https://github.com/mageplaza/magento-2-share-cart)
- [Get Support](https://github.com/mageplaza/magento-2-share-cart/issues)

## 2. FAQs 

**Q: I got an error: Mageplaza_Core has been already defined**

A: Read solution [here](https://github.com/mageplaza/module-core/issues/3)

**Q: How can customers use share button?** 

A: Customers only need to click on the button and paste the automated URL to anywhere they want to share.  

**Q: Where will the Share button appear on the website?**

A: Share button can be seen on **Minicart** and **Shopping Cart** page. 

**Q: What if I want to inform customers that the price possibly will change later?**

A: You can leave a message on **Warning Message** box (from the admin backend).

**Q: Can I add the time when the PDF file is downloaded?** 

A: Absolutely yes. In the backend, you can enable the adding timestamp suffix.

**Q: How the PDF button and Text button differ from each other?**

A: **Text** button only displays chosen items while **PDF** button offers more information about the store. Also, Text button will show a pop-up, meanwhile the **PDF** button is for file downloading.

## 3. How to install Share Cart extension for Magento 2

- Install via composer (recommend)

Run the following command in Magento 2 root folder:

```
composer require mageplaza/module-share-cart
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

## 4. Highlight Features

### Quick share by copy-and-paste 

**Share Cart Extension** allows the store owners to add an extra button which is **Share Cart** while a customer is processing their purchasing.

The button can be displayed in the **Minicart** section and **Shopping Cart Page**. By clicking this button, the customer can copy their shopping cart’ s URL and paste to a destination just in the blink of an eye. When the URL recipient clicks on the shared URL, their current shopping cart will be automatically added with the same items.

![Quick share by copy-and-paste](https://i.imgur.com/GO16T9C.png)

### A brief summary with Text button 

For the cart which contains a large number of items, **Share Cart** module allows the customers to view a shot summary easily with **Text** - another extra button.

When the customer hit the **Text** button on their **Shopping Cart Page**, a pop-up text can be seen, it appears as a purchase summarizing box.
This little simple button supports customers in taking a clear overview of chosen items with necessary information such as items’ name, price, quantity and the cart total.

![A brief summary with Text button](https://i.imgur.com/HpEIKl6.png)

### Quick Shopping cart PDF file publishing

**PDF** button is another extra button with the function to download. When the customer hits this button, a PDF file will be downloaded and stored automatically in the user's current device. In comparison to the **Text** button with quick view function, **PDF**'s function allows the customer to get detail information such as:
- Information of store: Company Name, Address, Email, Phone, VAT Information, Registered Number, and Warning Message
- Date of the purchase
- Purchase summary: Quantity, Price, Total, Stock ID, Descriptio

![Magento 2 Share Cart extension](https://i.imgur.com/mLNOyd8.png)

## 5. More features

### Update function 

**Update** button is for updating the shopping cart with the latest changes from the original cart. 

### Warning message offering

Admin is able to add a message to the PDF file, as a notice to customers (for instance, informing about the validation of the file downloaded). 

### Mobile responsive ability

The module is properly responsive with both mobile and desktop devices.

## 6. Full Magento 2 Share Cart Features

### For store owners
- Enable/ Disable the module
- Enable/disable the extension
- Be able to set the PDF file name
- Add the timestamp suffix which shows the PDF file downloaded time
- Add information of the store including Company name, Address, Phone, Email, VAT Number, Registered Number
- Display a warning message to notify customers in the PDF file 

### For customers
- Quickly and easily share the shopping cart
- Briefly view the shared shopping cart
- Download and store the PDF with adequate information

## 7. How to configure Share Cart in Magento 2

### 7.1 Configuration

- Access to your Magento 2 Admin Panel, navigate to `Store tab > Open Settings > Configuration `
- Click `Mageplaza Extensions > Share Cart > Configuration`, go to `General Configuration` section.

![Magento 2 Share Cart extension configure](https://i.imgur.com/CwdqEgU.png)

#### 7.1.1. General

- **Enable**: Choose `Yes` to enable the Module. If the module is turned on, all the features work well. Otherwise, all the options in admin panel and the module will not show. 
- **Enable**: Select `Yes` to enable the extension
- **File Name**: Insert name for PDF file. The PDF file will display the customer’s order information
- **Add Timestamp suffix**: Click `Yes` option to display the current time to upload PDF document
 
#### 7.1.2. Business Information

- **Company Name**: Insert your company name in this field
- **Address**: Fill the company’s location
- **VAT Number**: Provide information about Value Added Taxes number
- **Registered Number**: Insert your company registered number
- **Phone**: Insert phone number
- **Email**: Another needed contact detail is the email address
- **Warning Message**: This field is for the special content you want to notice in the PDF orders. For instance: Prices are correct at the time of generation, some possibly have been changed since.


### 7.2 Frontend

After activating the module, customers can use **Share Cart** button to deliver the URL to people which they want to share the cart. After sharing, there will be already-added items in the cart of the URL recipient. 

- **Share Cart** button displays in the **Minicart** section when adding items to cart.

![Magento 2 Share Cart module](https://i.imgur.com/eHIz44c.png)

- Display play **Share Cart** button on **Shopping Cart** page, customers can click this button to share the cart URL.

![Magento 2 Share Cart extension free](https://i.imgur.com/CeJ3eXe.png)

  - Customers can click **Update Shopping Cart** to re-update information and changes in the original cart.
  - Click **Text** button to see detail information about products and price.
  
  ![Magento 2 Share Cart Free](https://i.imgur.com/ioURPW6.png)
  
  - Click on **PDF** button to see the order information.
    
    **PDF library setting instruction**
    
You need to delete the generated file and run the following command:

`
composer require mpdf/mpdf
`

![Magento 2 Share Cart extension by mageplaza](https://i.imgur.com/9pOaeMf.png)


**Other free Magento 2 extensions on Github**
- [Magento 2 SEO Suite free](https://github.com/mageplaza/magento-2-seo)
- [Magento 2 Google Maps free](https://github.com/mageplaza/magento-2-google-maps)
- [Magento 2 Backend Reindex](https://github.com/mageplaza/magento-2-backend-reindex)
- [Magento 2 GDPR free](https://github.com/mageplaza/magento-2-gdpr)
- [Magento 2 login as customer free](https://github.com/mageplaza/magento-2-login-as-customer)
- [Magento 2 Social login free](https://github.com/mageplaza/magento-2-social-login)
- [Magento 2 Advanced report free](https://github.com/mageplaza/magento-2-reports)
- [Magento 2 Blog free](https://github.com/mageplaza/magento-2-blog)


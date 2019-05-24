<?php
  return [
    //应用ID,您的APPID。
		'app_id' => "2016100100636204",

		//商户私钥
		'merchant_private_key' => "MIIEogIBAAKCAQEAyI/rfU26hsOoIKA7g/7fYhtPSBAcdtlirVLcHWXlz0wcMNTbB6UXLqnXJ2hAbiHGpLmRq17cgyWdtxL6UAMKjk3MKPIAnPmWJ/B4HrqJDv2EGn9CrvVpnSiWAbhv3BJoXDfBW6dwDWN8YXUORgYblNc9ldQSvBzEXqnhkApCmnYw4zFl7TaD8d4kfdsNvXm6Rnw+C5xgevUm696NAQGykmR1aK7XCW5nWo9oaSRODrtQIFm+n/Pe6OAQYrTts8wdEkB5cf8tr5uALQe+22dBG8Ivh2GmX9bCDETTVxbXNZGJoec8eft7GP+2DWPAk1Db/c3PrA10cXcTbEswN6oYSQIDAQABAoIBACExIEnmeT4gV0y+99qKbbGwz1gfwnYw19HTarY6zOOXtvql33HOcp24YfEVocQYNuXACEmSM3BI42cO7voa5r5SRb1o/4z7CUym0VpUiKZAcoJoGUMXPllSBDFYsrp7GWZZm9htR3APzN/cHXadQCdLz9dh94/GOwFnn/rUl90z8xRODX6Vq6sR9y0/xmwzehkbT9sTCLxG0PnTXi4bhs8Uym982XcqOXeX8ofQyyB7BJ9TUQJqeVrMLw0Jc/JHugRqFv+aOSbsypakCmKzh8RT1gM28yZcmco3Jnpjdpg8SWVfI7fGahDzuntq1tKfxho47QsZcSoKedI4Yk4Zn1kCgYEA8Kj62BJKX+y3ruRb3Rfuf7Brn7X13H3IuI90cFoZT2iYQC3UX85aCcR1rHSzKbN+LRNhXMvhia06Grb7OxjoWDFEqV7cva1fdYnsSr09vtibYLNEaxUb/ufAeEuNKhmqECtWSUONz5y9OgCrnVEcjfUTGr3K+Gj4RIQQe7kQ2+cCgYEA1ViibOPdN7ALvBjiYNhLBWsG0vXlHZ1a8m3fa2eQU23g8WNHZT6sr35emvaVCzZqGTlAUROTisg9Wp3JoVV09//+5SLTTUjzcx5A9VntZ3PUqS4pQrg0+dXlcVZcef3cM2rAgiG/t95P548GWlxv3LEXD0h1pmO03N0hiBvwZE8CgYB8mF/WQhHze9DVYTEWVG+L+ECgHUq+7vheZRb5nAwCirpYb+HGAEWpTOdHc9vWOTGYELKVopCQAPlWH2oOfKS+FDPiJFTQdtQ3PELzpuoyxl4bQHSpo/IslLuXDDZ3l3XujSFNKQZgeFuXjgVLm1TXQgy0CZLt7RqsDluiUnwh/QKBgAQCtor1faMeXBodHaRUbJSdfnNYzAXSf3MvPZP3Tm9DEYd4Jk9w7i4eYgjnucWMMFJpERx1EcD6Iq0hajjuMlS3K0ODdBVv2aPAXdg+6IeZVdYYWA45CEXqD3Yh+XsRmqIcz6GioMgEFdx0g9oPAstTSOuwoQWPukasoYS9gbdhAoGAMKsaLz+hCSwKngZgIDva1wXepInYMlbney8HC7DXJjB8akJujd0qSmpGSVJojeUQyFqGOqR2weM2eofanUunvDnSRmkLh+csvDPDC/Gg/8NMQLOd+Z41gIhNIcDbceUWw8YNjcbOjNbhjRGS4yjkO1m/nfNFdZJ7Tf2Fn0NDoDE=",
		
		//异步通知地址
		'notify_url' => "http://39.107.115.219",
		
		//同步跳转
		'return_url' => "http://www.myshop.com/index.php/index/order/returnUrl",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAl5f9TgmS5t8ZVeN2AlTEm4OtCizjr4Ijnnbee2RgVg0HfrbU/g15EuGT4EiyYWbfRRffoHxF5ZO3ZqZgM+zyaoSasrkFl8lauNycPTUfqBGt2EtjgGzcBy1pnEo7z2hxo4boFWQjWAvfbTgEZ8yNi6Yct9OX9S84sxIHgTVgE+zLIYJMRTO8eCIeKmAoGpZzw2Q3MJJ/2ntLFJzETUz7366TgfLTJitmbxb7+/k1AjQSg0946D/s8vQnW/P/qmpD9WD4WPXnMAJH3O0e9KtEWcY+cpm2Sbox8QRm9UTcYsa+3wGzgPmfHyZxaDPvJe1CBIjjzbxbSXjGEvS6SRjTKwIDAQAB",
  ];
 
?>
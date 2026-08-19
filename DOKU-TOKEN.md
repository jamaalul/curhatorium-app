# B2B

### API Endpoint

To get access token, you need to hit this API endpoint :&#x20;

<table><thead><tr><th>Type</th><th>Value</th></tr></thead><tbody><tr><td>Service Code</td><td>73</td></tr><tr><td>HTTP Method</td><td>POST</td></tr><tr><td>Path</td><td><p></p><pre class="language-json"><code class="lang-json">/authorization/v1/access-token/b2b`
</code></pre></td></tr></tbody></table>

### API Request Header to get Token

```json
X-SIGNATURE: Pxlv2IIUVdlzdUnbSQqug8YeghmKXJ7Rw5P4xBOOB/tC457UsoZXkO4S1R3oszVcjZDSh38+==
X-TIMESTAMP: 2022-10-07T14:18:39+07:00
X-CLIENT-KEY: MCH-0008-1296507211683
Content-Type: application/json
```

#### Request Header Explanation&#x20;

<table><thead><tr><th>Parameter</th><th>Data Type</th><th>Type</th><th>Description</th></tr></thead><tbody><tr><td>X-Signature</td><td><p></p><pre class="language-json"><code class="lang-json"><strong>string
</strong></code></pre></td><td>Mandatory</td><td>Non-Repudiation &#x26; Integrity checking X-Signature : with asymmetric signature algorithm SHA256withRSA (Private_Key, stringToSign)<br><br><code>stringToSign = client_ID + “|” + X- TIMESTAMP</code></td></tr><tr><td>X-Timestamp</td><td><p></p><pre class="language-json"><code class="lang-json">string
</code></pre></td><td>Mandatory</td><td>Timestamp request on UTC time in ISO8601 UTC+0 format. It means to proceed transaction on UTC+7 (WIB), merchant need to subtract time with 7.<br><br>Ex: to proceed transaction on September 22th 2020 at 08:51:00 WIB, the timestamp should be 2020-09-22T01:51:00Z</td></tr><tr><td>X- Client-Key</td><td><p></p><pre class="language-json"><code class="lang-json">string
</code></pre></td><td>Mandatory</td><td><ol><li>Client’s client_id (PJP Name) (given at completion registration process)</li><li>Merchant to DOKU : client_id merchant</li></ol><p></p><pre class="language-json" data-overflow="wrap"><code class="lang-json">Acquirer to DOKU : client_key given by DOKU
</code></pre><pre class="language-mdx" data-overflow="wrap"><code class="lang-mdx">DOKU to Acquirer : client_key given by acquirer.
</code></pre></td></tr><tr><td>content-type</td><td><p></p><pre class="language-json"><code class="lang-json">string
</code></pre></td><td>Mandatory</td><td>String represents indicate the media type of the resource (e.g. application/json, application/pdf)</td></tr></tbody></table>

### API Request Body

Here is the sample of request body to Get Token :&#x20;

````json
{
"grantType":"client_credentials"
}
```
````

#### Request Body Explanation

<table><thead><tr><th>Parameter</th><th>Data Type</th><th>Type</th><th>Description</th></tr></thead><tbody><tr><td><p></p><pre class="language-json"><code class="lang-json">grantType
</code></pre></td><td>String</td><td>Mandatort</td><td>“client_credentials” : The client can request an access token using only its client credentials (or other supported means of authentication) when the client is requesting access to the protected resources under its control (OAuth 2.0: RFC 6749 &#x26; 6750)</td></tr></tbody></table>

### API Response Body

#### API Response Header

After hitting the above API Request, DOKU will give the response below

| Type        | Value   |
| ----------- | ------- |
| HTTP Status | 200     |
| Result      | Success |

```json
X-CLIENT-KEY: "MCH-0008-1296507211683",
X-TIMESTAMP: "2022-10-07T14:26:50+07:00"
```

<table><thead><tr><th>Parameter</th><th>Data Type</th><th>Type</th><th>Description</th></tr></thead><tbody><tr><td>X-Timestamp</td><td>String</td><td>Mandatory</td><td>Client's current local time in YYYY-MM-DDTHH:mm:ssZ format</td></tr><tr><td>X-Client-Key</td><td>String</td><td>Mandatory</td><td><p>Client’s client_id (PJP Name) (given at completion registration process)</p><p></p><pre class="language-mdx"><code class="lang-mdx">Merchant to DOKU : client_id merchant.
</code></pre><pre class="language-mdx"><code class="lang-mdx">DOKU to Acquirer : client_key given by acquirer.
</code></pre><pre class="language-mdx"><code class="lang-mdx">Acquirer to DOKU : client_key given by DOKU
</code></pre></td></tr></tbody></table>

#### API Response Body

```json
{
    "responseCode": "2007300",
    "responseMessage": "Successful",
    "accessToken": "eyJhbGciOiJSUzI1NiJ9.eyJleHAiOjE2NjUxMjc3OTEsIm5iZiI6MTY2NTEyNjg5MSwiaXNzIjoiRE9LVSIsImlhdCI6",
    "tokenType": "Bearer",
    "expiresIn": 900,
    "additionalInfo": ""
}
```

<table><thead><tr><th>Parameter</th><th>Data Type</th><th>Type</th><th>Description</th></tr></thead><tbody><tr><td><p></p><pre class="language-json"><code class="lang-json">responseCode
</code></pre></td><td>String (6)</td><td>Mandatory</td><td><p>Response Code : </p><pre class="language-json"><code class="lang-json">HTTP status code + service code + case code
</code></pre></td></tr><tr><td><p></p><pre class="language-json"><code class="lang-json">responseMessage
</code></pre></td><td>String</td><td>Mandatory</td><td>Response Description</td></tr><tr><td><p></p><pre class="language-json"><code class="lang-json">accessToken
</code></pre></td><td>String (2048)</td><td>Mandatory</td><td>A string representing an authorization issued to the client that used to access protected resources.</td></tr><tr><td><p></p><pre class="language-json"><code class="lang-json">tokenType
</code></pre></td><td>String</td><td>Mandatory</td><td>The access token type provides the client with the information required to successfully utilize the access token to make a protected resource request (along with type-specific attributes).<br><br>Token Type Value: “Bearer”: includes the access token.<br><br>string in the request “Mac”: issuing a Message.<br><br>Authentication Code (MAC) key together with the access token that is used to sign certain components of the HTTP requests. Reference: OAuth2.0 RFC 6749 &#x26; 6750</td></tr><tr><td><p></p><pre class="language-json"><code class="lang-json">expiresIn
</code></pre></td><td>String</td><td>Mandatory</td><td>Session expiry in seconds : 900 (15 minute )</td></tr><tr><td><p></p><pre class="language-json"><code class="lang-json">additionalInfo
</code></pre></td><td>String</td><td>Optional</td><td>Additional Information</td></tr></tbody></table>

#### Error Response

For several error cases, the response appear would be like below :&#x20;

{% tabs %}
{% tab title="X-Timestamp format no valid" %}

```json
"responseCode": "4017300",
"responseMessage": "Unauthorized. Unknown Client"
```

{% endtab %}

{% tab title="Signature not valid" %}

```json
"responseCode": "4017300",
"responseMessage": "Unauthorized. Signature"
```

{% endtab %}

{% tab title="X-Client Key not authorized" %}

````json
"responseCode": "4017300",
    "responseMessage": "Unauthorized. Unknown Client"
```
````

{% endtab %}
{% endtabs %}

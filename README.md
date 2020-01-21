# PrintABrick
Web catalogue of LEGO® parts for 3D printing

Made a  low cost version of site, yet to add upload function.
As of rn, has 5000 bricks or something.
[link to site.](https://printabrick.000webhostapp.com/)

Made a print a brick API today _(mainly to teach myself aws)_
It currently **only has download,** no upload as of yet.
### Get File API:
`https://0sb44une91.execute-api.ap-southeast-2.amazonaws.com/testing/download/**model_no**`
### Get a list of all files: (json)
[https://0sb44une91.execute-api.ap-southeast-2.amazonaws.com/testing/download/list](https://0sb44une91.execute-api.ap-southeast-2.amazonaws.com/testing/download/list)

**Example:**

NXT MOTOR: **model id 53787**
`https://0sb44une91.execute-api.ap-southeast-2.amazonaws.com/testing/download/53787`

Door thingo: **model id: 2400**
`https://0sb44une91.execute-api.ap-southeast-2.amazonaws.com/testing/download/2400`

_by the way, if its slow its its because I throttled the rate to 1/second as i don't wan't to get billed if someone decides to screw with me. But once people start using it, i will up this
### Whats the point of this?
.
This API is AWS server less, so basically no maintenance, and low cost. 

**$3.50 for 1,000,000 API requests**
**S3 storage is like 0.025/gb**



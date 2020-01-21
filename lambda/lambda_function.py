import json
import boto3
from botocore.vendored import requests
s3 = boto3.client('s3')
def getKey(identity):
    data = {}
    with open('list.json') as f:
        data = json.load(f)
    if identity in data:
        return data[identity], True
    else:
        return 'err', False

def lambda_handler(event, context):
    key, ok = getKey(str(event['pathParameters']['id']))
    #str(event['pathParameters']['id'])
    if ok:
        url = s3.generate_presigned_url(
            ClientMethod='get_object',
            Params={
                'Bucket': 'printabrick',
                'Key': key
                }
                )
        # TODO implement
        response = {}
        response["statusCode"]=302
        response["headers"]={'Location': url}
        data = {}
        response["body"]=json.dumps(data)
        return response
    else:
        url = 'File Not Found'
    
        return {
            'statusCode': 404,
            'body': json.dumps(url)
        }

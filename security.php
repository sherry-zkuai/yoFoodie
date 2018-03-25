<?php

// For remember me option

	function headerGen()
	{
		$obj=array(
			'typ'=>'JWT',
            'alg'=>'HS256'
        );
        $json=base64_encode(json_encode($obj));
        return $json;
	}
    function payloadGen($username, $id)
	{
        $obj=array(
			'exp'=>time() + 120,
			'username'=>$username,
            'id'=>$id
        );
        $json=base64_encode(json_encode($obj));
        return $json;
    }

    function token($username, $id)
	{
        $arr=array();
		$header = headerGen();
        $payload=payloadGen($username, $id);
        $signiture=hash_hmac('sha256',$header.'.'.$payload,'comp307');
        $token=$header.'.'.$payload.'.'.$signiture;
        return $token;
    }

	function veriSigniture($header, $payload, $sign)
	{
		$rehashed_sig=hash_hmac('sha256',$header.$payload,'comp307');
		if (strcmp($rehashed_sig, $sign))
			return true;
		else 
			return false;
	}
	
    function verifyToken($jwt)
	{
        $arr=explode('.',$jwt);
        if(count($arr)!=3)
		{
            // throw new Exception("Wrong number of segments");
			return false;
        }
		list($header,$payload,$sign)=$arr;
		$header_json=json_decode(base64_decode($header),true);
        $payload_json=json_decode(base64_decode($payload),true);
        if(empty($payload_json) || empty($header_json) || !isset($payload_json['id']) || !isset($payload_json['exp']))
		{
            // throw new Exception("segment null");
			return false;
        }
		if (veriSigniture($header, $payload, $sign) && $payload_json['exp'] > time())
		{
			return true;
		}
		else
		{
			return false;
		}
		
    }
	
	function decodePayload($jwt)
	{
		$arr=explode('.',$jwt);
        list($header,$payload,$sign)=$arr;
		return json_decode(base64_decode($payload),true);
	}
?>
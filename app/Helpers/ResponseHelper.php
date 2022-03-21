<?php
    /**
     * return response custom.
     *
     * @return \Illuminate\Http\Response
     */
    function CreateResponse($data, $message, $code = 0, $http_code = 200)
    {
        $response = [
            'code'      => $code,
            'message'   => $message,
            'data'      => $data,
        ];

        return response()->json($response, $http_code);
    }
?>

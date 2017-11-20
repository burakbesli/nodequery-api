<?php

	class ApiSystem
	{
		private $myApi =  "";
		private $apiUrl = "https://nodequery.com/api/servers/";
		public function formatSizeUnits($bytes)
		    {
		        if ($bytes >= 1073741824)
		        {
		            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
		        }
		        elseif ($bytes >= 1048576)
		        {
		            $bytes = number_format($bytes / 1048576, 2) . ' MB';
		        }
		        elseif ($bytes >= 1024)
		        {
		            $bytes = number_format($bytes / 1024, 2) . ' KB';
		        }
		        elseif ($bytes > 1)
		        {
		            $bytes = $bytes . ' bytes';
		        }
		        elseif ($bytes == 1)
		        {
		            $bytes = $bytes . ' byte';
		        }
		        else
		        {
		            $bytes = '0 bytes';
		        }

		        return $bytes;
		}

		public function islem()
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,"{$this->apiUrl}?api_key={$this->myApi}");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec ($ch);
			curl_close ($ch);
			$server_output = json_decode($server_output);
			return $server_output;
		}

		public function detay($id)
		{
			$this->apiUrl = $this->apiUrl.$id."/";
			return $this;
		}


		public function test()
		{
			echo "test";
		}


	}

	

?>
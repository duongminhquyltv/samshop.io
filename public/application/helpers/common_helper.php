  <?php
  function public_url($url = '')
  {
  	return base_url('public/'.$url);
  }

  function pre($list, $exit = true)
  {
      echo "<pre>";
      print_r($list);
      if($exit)
      {
          die();
      }
  }
  //Cat the ky tu
  function limit_text($text, $limit) {
      $strings = preg_replace('/([^\pL\.\ ]+)/u', '', strip_tags($text));  
        if (strlen($text) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);
            if(sizeof($pos) >$limit)
            {
              $text = substr($text, 0, $pos[$limit]) . '...';
            }
            return $text;
        }
        return $text;
  }
  //Cat theo tu
  function limitWords($text, $limit) {
      $text = preg_replace('/([^\pL\.\ ]+)/u', '', strip_tags($text));  
      $word_arr = explode(" ", $text);

      if (count($word_arr) > $limit) {
          $words = implode(" ", array_slice($word_arr , 0, $limit) ) . ' ...';
          return $words;
      }

      return $text;
  }
  //cat theo tu co link
  function summaryMode($text, $limit, $more, $link) {
      if (str_word_count($text, 0) > $limit) {
          $numwords = str_word_count($text, 2);
          $pos = array_keys($numwords);
          $text = substr($text, 0, $pos[$limit]).'... <a href="'.$link.'">'.$more.'</a>';
      }
      return $text;
  }

  function full_path()
  {
      $s = &$_SERVER;
      $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
      $sp = strtolower($s['SERVER_PROTOCOL']);
      $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
      $port = $s['SERVER_PORT'];
      $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
      $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
      $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
      $uri = $protocol . '://' . $host . $s['REQUEST_URI'];
      $segments = explode('?', $uri, 2);
      $url = $segments[0];
      return $url;
  }

  function format_phone($phone)
  {
      $phone = preg_replace("/^\d/", "", $phone);

      if(strlen($phone) == 7)
          return preg_replace("/(\d{3})(\d{4})/", "$1-$2", $phone);
      elseif(strlen($phone) == 10)
          return preg_replace("/(\d{3})(\d{3})(\d{4})/", "($1) $2-$3", $phone);
      else
          return $phone;
  }
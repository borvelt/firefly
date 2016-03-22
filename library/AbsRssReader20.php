<?php
class AbsRssReader20
{
	private function __clone(){}
	public function __construct(){}
	protected static $_doc = null;
	protected static $_loaded = FALSE;
	final public function Load( $filePath )
	{
		if (is_null($filePath) or strlen($filePath) < 1)
			exit("Error in ".__CLASS__.'::'.__FUNCTION__.'<br/>The path to the rss file is missing!');

		self::$_doc = new DOMDocument();
		if (@self::$_doc->load($filePath))
			self::$_loaded = TRUE;
		else exit("خطا در باز کردن اطلاعات");
	}

	final public function GetChannelTags()
	{
		$result = array();
		if ( ! self::$_loaded) return $result;

		$ch = self::$_doc->getElementsByTagName('channel')->item(0);
		if ( ! is_null($ch))
		{
			if ($ch->hasChildNodes())
			{
				foreach ($ch->getElementsByTagName('*') as $tag)
				{
					if ($tag->hasChildNodes() and ($tag->tagName <> 'item'))
						$result[$tag->tagName] = html_entity_decode($tag->nodeValue, ENT_QUOTES, 'UTF-8') ;
				}
			}
		}
		return $result;
	}

	final public function GetItems( $maxLimit = 0 )
	{
		$result = array();
		if ( ! self::$_loaded) return $result;

		$ch = self::$_doc->getElementsByTagName('channel')->item(0);
		if ( ! is_null($ch))
		{
			if ($ch->hasChildNodes())
			{
				$i = 0;
				foreach ($ch->getElementsByTagName('item') as $tag)
				{
					$result['item_'.$i] = array();
					
					foreach ($tag->getElementsByTagName('*') as $item)
						$result['item_'.$i][$item->tagName] = html_entity_decode($item->nodeValue, ENT_QUOTES, 'UTF-8') ;

					$i++;
					if ($maxLimit == $i) break;
				}
			}
		}
		return $result;
	}

	final public function GetAll()
	{
		$result = array();
		if ( ! self::$_loaded) return $result;

		$channelTags = $this->GetChannelTags();
		$channelItems = $this->GetItems();

		$result = array_merge($channelTags, $channelItems);

		return $result;
	}

}
?>
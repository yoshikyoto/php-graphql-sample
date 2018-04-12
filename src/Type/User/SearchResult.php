<?php

namespace nicogql\type\video;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use nicogql\type\NicovideoType;

class SearchResult extends ObjectType {

	public $videos;

	public function __construct() {
		parent::__construct([
			'name' => 'SearchResult',
			'fields' => [
				'videos' => [
					'type' => Type::listOf(NicovideoType::video()),
					'resolve' => function () {
						// とりあえず検索結果は固定で返す
						return ['sd278138', 'so257798'];
					},
				],
			],
		]);
	}

}

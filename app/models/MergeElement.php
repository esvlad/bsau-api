<?php

namespace Esvlad\BsauApi\Models;

use Illuminate\Database\Capsule\Manager as Capsule;

use Esvlad\BsauApi\Models\{
	File,
	IBlock,
	IBlockProperty,
	IBlockElement,
	IBlockSection,
	IBlockPropertyEnum
};

class MergeElement {
	public static function merge(int $iblock_id){
		$iblock_old = IBlock::get($iblock_id);

		//Проверить инфоблок в новой базе
		$iblock_new = IBlock::has($iblock_old->NAME, $iblock_old->CODE);

		if($iblock_new === false){
			throw new MyException("Инфоблок " . $iblock_old->NAME . " отсутствует, ID старого инфоблока " . $iblock_id);			
		}

		//Достать свойства старого инфоблока
		$iblock_old_properties = IBlockProperty::get($iblock_id);

		//Достать свойства нового инфоблока
		$iblock_new_properties = IBlockProperty::has($iblock_new->ID);

		$old_properties = self::propertyСollector($iblock_old_properties);
		$new_properties = self::propertyСollector($iblock_new_properties);

		$diff_properties = self::differenceProperties($old_properties, $new_properties);

		$arr = [
			'IBLOCK_OLD' => [
				'IBLOCK' => [
					'ID' => $iblock_old->ID,
					'NAME' => $iblock_old->NAME,
					'CODE' => $iblock_old->CODE,
				],
				//'PROPERTY' => $old_properties,
			],
			'IBLOCK_NEW' => [
				'IBLOCK' => [
					'ID' => $iblock_new->ID,
					'NAME' => $iblock_new->NAME,
					'CODE' => $iblock_new->CODE,
				],
				//'PROPERTY' => $new_properties,
			],
			'DIFF_PROPERTY' => $diff_properties
		];

		print_r($arr);

		//Сравнить свойства инфоблоков, если есть разница выбросить исключение
	}

	public static function propertyСollector($properties) : object {
		$result = [];
		foreach($properties as $property){
			$result[] = (object)[
				'ID' => $property->ID,
				'IBLOCK_ID' => $property->IBLOCK_ID,
				'NAME' => $property->NAME,
				'CODE' => $property->CODE,
				'PROPERTY_TYPE' => $property->PROPERTY_TYPE,
				'XML_ID' => $property->XML_ID
			];
		}

		return (object)$result;
	}

	public static function differenceProperties($old_properties, $new_properties) : object | bool {
		$diff_result = [];

		foreach($old_properties as $old_property){
			$diff_result[$old_property->CODE] = false;
			foreach($new_properties as $new_property){
				if($old_property->CODE == $new_property->CODE || $old_property->NAME == $new_property->NAME || $old_property->ID == $new_property->XML_ID){
					unset($diff_result[$old_property->CODE]);
					break;
				}
			}
		}

		if(!empty($diff_result)){
			$result = [];
			foreach($diff_result as $key => $value){
				foreach($old_properties as $old_property){
					if($old_property->CODE == $key){
						$result[] = (object)[
							'ID' => $old_property->ID,
							'IBLOCK_ID' => $old_property->IBLOCK_ID,
							'NAME' => $old_property->NAME,
							'CODE' => $old_property->CODE,
							'PROPERTY_TYPE' => $old_property->PROPERTY_TYPE
						];
						break;
					}
				}
			}

			return (object)$result;
		}

		return false;
	}

	public static function mergeVideo(){
		$elements = IBlockElement::select('ID', 'XML_ID')->where('IBLOCK_ID', 53)->where('NAME', 'like', 'Защита диссертации%')->get();

		$element_props = [];
		foreach($elements as $element){
			$new_element_id = IBlockElement::getNewID($element->XML_ID);
			$props = IBlockElementProperty::where('IBLOCK_ELEMENT_ID', $element->ID)->get();

			$arprop = [];
			foreach($props as $prop){
				if($prop->IBLOCK_PROPERTY_ID == 599){
					$new_value = IBlockElement::getNewIDByOldID($prop->VALUE);
					$arprop[] = [
						'ID' => NULL,
						'IBLOCK_PROPERTY_ID' => 524,
						'IBLOCK_ELEMENT_ID' => $new_element_id,
						'VALUE' => $new_value,
						'VALUE_TYPE' => $prop->VALUE_TYPE,
						'VALUE_NUM' => (float)$new_value
					];
				}
			}

			$element_props[] = $arprop;
		}

		//print_r($element_props);

		$path = getStorage('video');
		$filename = $path . 'sql.txt';

		/*foreach($element_props as $key => $value){
			foreach($value as $eprop){
				$sql = "INSERT INTO `b_iblock_element_property`(`ID`, `IBLOCK_PROPERTY_ID`, `IBLOCK_ELEMENT_ID`, `VALUE`, `VALUE_TYPE`, `VALUE_NUM`) VALUES (NULL, {$eprop['IBLOCK_PROPERTY_ID']}, {$eprop['IBLOCK_ELEMENT_ID']}, {$eprop['VALUE']}, '{$eprop['VALUE_TYPE']}', {$eprop['VALUE_NUM']});";

				file_put_contents($filename, PHP_EOL . $sql, FILE_APPEND);
			}
		}*/
	}

	public static function mergeDisSov(){
		$elements = IBlockElement::select('ID', 'XML_ID')->where('IBLOCK_ID', 82)->get();
		$iblock_property = IBlockProperty::get(82);
		$iblock_property_new = IBlockProperty::getNew(40);

		$element_props = [];
		foreach($elements as $element){
			$new_element_id = IBlockElement::getNewID($element->XML_ID);
			$props = IBlockElementProperty::getProps($element->ID);

			$path = getStorage('dis');
			$filename = $path . 'sql.txt';

			$arprop = [];
			foreach($props as $prop){
				$prop_type = false;
				$prop_code = false;

				foreach($iblock_property as $ibprop){
					if($ibprop->ID == $prop->IBLOCK_PROPERTY_ID){
						$prop_type = $ibprop->PROPERTY_TYPE;
						$prop_code = $ibprop->CODE;
						break;
					}
				}

				if(!empty($prop_type)){
					switch ($prop_type) {
						case 'E':
							$new_value = IBlockElement::getNewIDByOldID($prop->VALUE);

							$aprop = [
								'ID' => NULL,
								'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID(40, $prop_code),
								'IBLOCK_ELEMENT_ID' => $new_element_id,
								'VALUE' => $new_value,
								'VALUE_TYPE' => $prop->VALUE_TYPE,
								'VALUE_NUM' => $new_value
							];

							$arprop[] = $aprop;

							$sql = "INSERT INTO `b_iblock_element_property`(`ID`, `IBLOCK_PROPERTY_ID`, `IBLOCK_ELEMENT_ID`, `VALUE`, `VALUE_TYPE`, `VALUE_NUM`) VALUES (NULL, {$aprop['IBLOCK_PROPERTY_ID']}, {$aprop['IBLOCK_ELEMENT_ID']}, '{$aprop['VALUE']}', '{$aprop['VALUE_TYPE']}', '{$aprop['VALUE_NUM']}');";
							file_put_contents($filename, PHP_EOL . $sql, FILE_APPEND);

							break;

						case 'L':
							$new_value = IBlockPropertyEnum::getNewIDByXMLID($prop->IBLOCK_PROPERTY_ID, $prop->VALUE_ENUM);

							$aprop = [
								'ID' => NULL,
								'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID(40, $prop_code),
								'IBLOCK_ELEMENT_ID' => $new_element_id,
								'VALUE' => $new_value,
								'VALUE_TYPE' => $prop->VALUE_TYPE,
								'VALUE_ENUM' => $new_value,
								'VALUE_NUM' => $new_value
							];

							$arprop[] = $aprop;

							$sql = "INSERT INTO `b_iblock_element_property`(`ID`, `IBLOCK_PROPERTY_ID`, `IBLOCK_ELEMENT_ID`, `VALUE`, `VALUE_TYPE`, `VALUE_ENUM`, `VALUE_NUM`) VALUES (NULL, {$aprop['IBLOCK_PROPERTY_ID']}, {$aprop['IBLOCK_ELEMENT_ID']}, '{$aprop['VALUE']}', '{$aprop['VALUE_TYPE']}', '{$aprop['VALUE_ENUM']}', '{$aprop['VALUE_NUM']}');";
							file_put_contents($filename, PHP_EOL . $sql, FILE_APPEND);

							break;

						case 'F':
							$new_value = File::getNewIDByXMLID($prop->VALUE);

							$aprop = [
								'ID' => NULL,
								'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID(40, $prop_code),
								'IBLOCK_ELEMENT_ID' => $new_element_id,
								'VALUE' => $new_value,
								'VALUE_TYPE' => $prop->VALUE_TYPE,
								'VALUE_NUM' => $new_value
							];

							$arprop[] = $aprop;

							$sql = "INSERT INTO `b_iblock_element_property`(`ID`, `IBLOCK_PROPERTY_ID`, `IBLOCK_ELEMENT_ID`, `VALUE`, `VALUE_TYPE`, `VALUE_NUM`) VALUES (NULL, {$aprop['IBLOCK_PROPERTY_ID']}, {$aprop['IBLOCK_ELEMENT_ID']}, '{$aprop['VALUE']}', '{$aprop['VALUE_TYPE']}', '{$aprop['VALUE_NUM']}');";
							file_put_contents($filename, PHP_EOL . $sql, FILE_APPEND);

							break;
						
						default:
							$aprop = [
								'ID' => NULL,
								'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID(40, $prop_code),
								'IBLOCK_ELEMENT_ID' => $new_element_id,
								'VALUE' => $prop->VALUE,
								'VALUE_TYPE' => $prop->VALUE_TYPE,
								'VALUE_ENUM' => $prop->VALUE_ENUM,
								'VALUE_NUM' => $prop->VALUE_NUM
							];

							$arprop[] = $aprop;

							$sql = "INSERT INTO `b_iblock_element_property`(`ID`, `IBLOCK_PROPERTY_ID`, `IBLOCK_ELEMENT_ID`, `VALUE`, `VALUE_TYPE`, `VALUE_ENUM`, `VALUE_NUM`) VALUES (NULL, {$aprop['IBLOCK_PROPERTY_ID']}, {$aprop['IBLOCK_ELEMENT_ID']}, '{$aprop['VALUE']}', '{$aprop['VALUE_TYPE']}', '{$aprop['VALUE_ENUM']}', '{$aprop['VALUE_NUM']}');";
							file_put_contents($filename, PHP_EOL . $sql, FILE_APPEND);

							break;
					}
				}
			}

			$element_props[] = $arprop;
		}

		//print_r($element_props);

		

		/*foreach($element_props as $key => $value){
			foreach($value as $eprop){
				$sql = "INSERT INTO `b_iblock_element_property`(`ID`, `IBLOCK_PROPERTY_ID`, `IBLOCK_ELEMENT_ID`, `VALUE`, `VALUE_TYPE`, `VALUE_NUM`) VALUES (NULL, {$eprop['IBLOCK_PROPERTY_ID']}, {$eprop['IBLOCK_ELEMENT_ID']}, {$eprop['VALUE']}, '{$eprop['VALUE_TYPE']}', {$eprop['VALUE_NUM']});";

				file_put_contents($filename, PHP_EOL . $sql, FILE_APPEND);
			}
		}*/
	}

	public static function mergeXMLID(){
		$new_elements = IBlockElement::listNew(47);
		$old_elements = IBlockElement::where('IBLOCK_ID', 49)->get(); //->where('ACTIVE', 'Y')

		$edit_elements = [];
		foreach($new_elements as $new_element){
			foreach($old_elements as $old_element){
				if(mb_strtolower(trim($new_element->NAME)) == mb_strtolower(trim($old_element->NAME))){
					$edit_elements[] = [
						'ID' => $new_element->ID,
						'UPDATE' => [
							'XML_ID' => $old_element->XML_ID
						]
					];
					break;
				}
			}
		}

		if(!empty($edit_elements)){
			$path = getStorage();
			$filename = $path . 'disciplin.txt';

			foreach($edit_elements as $edit_element){
				IBlockElement::newEdit($edit_element['ID'], $edit_element['UPDATE']);

				$sql = "UPDATE `b_iblock_element` SET `XML_ID`= '{$edit_element['UPDATE']['XML_ID']}' WHERE `ID` = {$edit_element['ID']};";
				file_put_contents($filename, PHP_EOL . $sql, FILE_APPEND);
			}

			print_r($edit_elements);
		}
	}

	public static function mergeElementProperty(int $OLD_IBLOCK_ID, int $NEW_IBLOCK_ID, $IBLOCK_CODE){
		IBlockElement::select('ID', 'XML_ID')->where('IBLOCK_ID', $OLD_IBLOCK_ID)->chunk(100, function($elements) use ($OLD_IBLOCK_ID, $NEW_IBLOCK_ID, $IBLOCK_CODE){
			$iblock_property = IBlockProperty::get($OLD_IBLOCK_ID);

			$element_props = [];
			foreach($elements as $element){
				$new_element_id = IBlockElement::getNewID($element->XML_ID);
				if(empty($new_element_id)) continue;

				$props = IBlockElementProperty::getProps($element->ID);

				$arprop = [];
				foreach($props as $prop){
					$prop_type = false;
					$prop_code = false;

					foreach($iblock_property as $ibprop){
						if($ibprop->ID == $prop->IBLOCK_PROPERTY_ID){
							$prop_type = $ibprop->PROPERTY_TYPE;
							$prop_code = $ibprop->CODE;
							break;
						}
					}

					if(!empty($prop_type)){
						switch ($prop_type) {
							case 'E':
								$new_value = IBlockElement::getNewIDByOldID($prop->VALUE);

								if(empty($new_value)) break;

								$aprop = [
									'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID($NEW_IBLOCK_ID, $prop_code),
									'IBLOCK_ELEMENT_ID' => $new_element_id,
									'VALUE' => $new_value,
									'VALUE_TYPE' => $prop->VALUE_TYPE,
									'VALUE_NUM' => $new_value
								];

								//$arprop[] = $aprop;

								IBlockElementProperty::setElementPropertyBsau3($aprop);

								break;

							case 'L':
								$new_value = IBlockPropertyEnum::getNewIDByXMLID($prop->IBLOCK_PROPERTY_ID, $prop->VALUE_ENUM);

								$aprop = [
									'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID($NEW_IBLOCK_ID, $prop_code),
									'IBLOCK_ELEMENT_ID' => $new_element_id,
									'VALUE' => $new_value,
									'VALUE_TYPE' => $prop->VALUE_TYPE,
									'VALUE_ENUM' => $new_value,
									'VALUE_NUM' => $new_value
								];

								//$arprop[] = $aprop;

								IBlockElementProperty::setElementPropertyBsau3($aprop);

								break;

							case 'N':
								if($prop->VALUE == 1){
									$new_value = 'Y';
								} else {
									$new_value = 'N';
								}

								$aprop = [
									'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID($NEW_IBLOCK_ID, $prop_code),
									'IBLOCK_ELEMENT_ID' => $new_element_id,
									'VALUE' => $new_value,
									'VALUE_TYPE' => 'text',
								];

								//$arprop[] = $aprop;

								IBlockElementProperty::setElementPropertyBsau3($aprop);

								break;

							case 'F':
								if(empty($prop->VALUE)) break;
								$new_value = File::getNewIDByXMLID((int)$prop->VALUE, $IBLOCK_CODE);
								
								if($new_value === false) break;

								$aprop = [
									'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID($NEW_IBLOCK_ID, $prop_code),
									'IBLOCK_ELEMENT_ID' => $new_element_id,
									'VALUE' => $new_value,
									'VALUE_TYPE' => $prop->VALUE_TYPE,
									'VALUE_NUM' => $new_value
								];

								//$arprop[] = $aprop;

								IBlockElementProperty::setElementPropertyBsau3($aprop);

								break;

							case 'G':
								if(empty($prop->VALUE)) break;
								$new_value = IBlockSection::getNewID((int)$prop->VALUE);
								
								if($new_value === false) break;

								$aprop = [
									'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID($NEW_IBLOCK_ID, $prop_code),
									'IBLOCK_ELEMENT_ID' => $new_element_id,
									'VALUE' => $new_value,
									'VALUE_TYPE' => $prop->VALUE_TYPE,
									'VALUE_NUM' => $new_value
								];

								//$arprop[] = $aprop;

								IBlockElementProperty::setElementPropertyBsau3($aprop);

								break;
							
							default:
								$aprop = [
									'ID' => NULL,
									'IBLOCK_PROPERTY_ID' => IBlockProperty::getNewID($NEW_IBLOCK_ID, $prop_code),
									'IBLOCK_ELEMENT_ID' => $new_element_id,
									'VALUE' => $prop->VALUE,
									'VALUE_TYPE' => $prop->VALUE_TYPE,
									'VALUE_ENUM' => $prop->VALUE_ENUM,
									'VALUE_NUM' => $prop->VALUE_NUM
								];

								//$arprop[] = $aprop;

								IBlockElementProperty::setElementPropertyBsau3($aprop);

								break;
						}
					}
				}

				//$element_props[] = $arprop;
			}
		});

		//print_r($element_props);
	}

	public static function setProperty($OLD_IBLOCK_ID, $NEW_IBLOCK_ID, $IBLOCK_CODE = false){
		$iblock_property = IBlockProperty::where('IBLOCK_ID', $OLD_IBLOCK_ID)->get();

		/*$new_property = [];
		foreach($iblock_property as $property){
			if($property->PROPERTY_TYPE == 'N') continue;

			$new_property[] = [
				'TIMESTAMP_X' => $property->TIMESTAMP_X,
				'IBLOCK_ID' => 59,
				'NAME' => $property->NAME,
				'ACTIVE' => $property->ACTIVE,
				'SORT' => $property->SORT,
				'CODE' => $property->CODE,
				'DEFAULT_VALUE' => $property->DEFAULT_VALUE,
				'PROPERTY_TYPE' => $property->PROPERTY_TYPE,
				'ROW_COUNT' => $property->ROW_COUNT,
				'COL_COUNT' => $property->COL_COUNT,
				'LIST_TYPE' => $property->LIST_TYPE,
				'MULTIPLE' => $property->MULTIPLE,
				'XML_ID' => $property->XML_ID,
				'FILE_TYPE' => $property->FILE_TYPE,
				'MULTIPLE_CNT' => $property->MULTIPLE_CNT,
				'TMP_ID' => $property->TMP_ID,
				'LINK_IBLOCK_ID' => $property->LINK_IBLOCK_ID,
				'WITH_DESCRIPTION' => $property->WITH_DESCRIPTION,
				'SEARCHABLE' => $property->SEARCHABLE,
				'FILTRABLE' => $property->FILTRABLE,
				'IS_REQUIRED' => $property->IS_REQUIRED,
				'VERSION' => $property->VERSION,
				'USER_TYPE' => $property->USER_TYPE,
				'USER_TYPE_SETTINGS' => $property->USER_TYPE_SETTINGS,
				'HINT' => $property->HINT,
			];
		}

		print_r($new_property);
		IBlockProperty::setNewProperty($new_property);*/

		$new_property_enum = [];
		foreach($iblock_property as $property){
			
			if($property->PROPERTY_TYPE == 'N') continue;

			if($property->PROPERTY_TYPE == 'L'){

				$new_property_id = IBlockProperty::getNewID($NEW_IBLOCK_ID, $property->CODE);
				$old_property_enum = IBlockPropertyEnum::where('PROPERTY_ID', $property->ID)->get();
				
				foreach($old_property_enum as $property_enum){
					$new_property_enum[] = [
						'PROPERTY_ID' => $new_property_id,
						'VALUE' => $property_enum->VALUE,
						'DEF' => $property_enum->DEF,
						'SORT' => $property_enum->SORT,
						'XML_ID' => $property_enum->XML_ID,
						'TMP_ID' => !empty($property_enum->TMP_ID) ? $property_enum->TMP_ID : '',
					];
				}
			}
		}

		//print_r($new_property_enum);

		//IBlockPropertyEnum::setNewPropertyEnum($new_property_enum);
	}

	public static function mergeSection($OLD_IBLOCK_ID, $NEW_IBLOCK_ID, $IBLOCK_CODE = false){
		$old_sections = IBlockSection::getList($OLD_IBLOCK_ID);

		$path = getStorage('');
		$filename = $path . $IBLOCK_CODE . '.txt';

		$sections = [];
		foreach($old_sections as $section){
			$new_section = [
				'TIMESTAMP_X' => $section->TIMESTAMP_X, 
				'MODIFIED_BY' => 1, 
				'DATE_CREATE' => $section->DATE_CREATE, 
				'CREATED_BY' => 1, 
				'IBLOCK_ID' => $NEW_IBLOCK_ID, 
				'IBLOCK_SECTION_ID' => $section->IBLOCK_SECTION_ID, 
				'ACTIVE' => $section->ACTIVE, 
				'GLOBAL_ACTIVE' => $section->GLOBAL_ACTIVE, 
				'SORT' => $section->SORT, 
				'NAME' => $section->NAME, 
				'PICTURE' => $section->PICTURE, 
				'LEFT_MARGIN' => $section->LEFT_MARGIN, 
				'RIGHT_MARGIN' => $section->RIGHT_MARGIN, 
				'DEPTH_LEVEL' => $section->DEPTH_LEVEL, 
				'DESCRIPTION' => $section->DESCRIPTION, 
				'DESCRIPTION_TYPE' => $section->DESCRIPTION_TYPE, 
				'SEARCHABLE_CONTENT' => $section->SEARCHABLE_CONTENT, 
				'CODE' => $section->CODE, 
				'XML_ID' => $section->XML_ID, 
				'TMP_ID' => $section->TMP_ID, 
				'DETAIL_PICTURE' => $section->DETAIL_PICTURE, 
				'SOCNET_GROUP_ID' => $section->SOCNET_GROUP_ID
			];

			$sections[] = $new_section;

			$sql = IBlockSection::getSql($new_section);
			file_put_contents($filename, PHP_EOL . $sql, FILE_APPEND);
		}

		//print_r($sections);
	}

	public static function mergeElements($OLD_IBLOCK_ID, $NEW_IBLOCK_ID, $IBLOCK_CODE = false){
		//$old_elements = IBlockElement::getList($OLD_IBLOCK_ID);

		IBlockElement::where('IBLOCK_ID', $OLD_IBLOCK_ID)->orderBy('ID')->chunk(100, function($old_elements){
			$new_elements = [];
			foreach($old_elements as $element){
				$new_element = [
					'TIMESTAMP_X' => $element->TIMESTAMP_X,
					'MODIFIED_BY' => 1,
					'DATE_CREATE' => $element->DATE_CREATE,
					'CREATED_BY' => 1,
					'IBLOCK_ID' => 66,
					'IBLOCK_SECTION_ID' => IBlockSection::getNewID($element->IBLOCK_SECTION_ID),
					'ACTIVE' => $element->ACTIVE,
					'ACTIVE_FROM' => $element->ACTIVE_FROM,
					'ACTIVE_TO' => $element->ACTIVE_TO,
					'SORT' => $element->SORT,
					'NAME' => $element->NAME,
					'PREVIEW_PICTURE' => !empty($element->PREVIEW_PICTURE) ? File::getNewIDByXMLID($element->PREVIEW_PICTURE, 'photo_smi') : null,
					'PREVIEW_TEXT' => $element->PREVIEW_TEXT,
					'PREVIEW_TEXT_TYPE' => $element->PREVIEW_TEXT_TYPE,
					'DETAIL_PICTURE' => !empty($element->DETAIL_PICTURE) ? File::getNewIDByXMLID($element->DETAIL_PICTURE, 'photo_smi') : null,
					'DETAIL_TEXT' => $element->DETAIL_TEXT,
					'DETAIL_TEXT_TYPE' => $element->DETAIL_TEXT_TYPE,
					'SEARCHABLE_CONTENT' => $element->SEARCHABLE_CONTENT,
					'IN_SECTIONS' => $element->IN_SECTIONS,
					'XML_ID' => $element->XML_ID,
					'CODE' => $element->CODE,
					'TMP_ID' => $element->TMP_ID,
				];

				$new_elements[] = $new_element;
			}

			IBlockElement::setElementBsau3($new_elements);
		});
	}

	public static function editElements($IBLOCK_ID){
		IBlockElement::updateSection($IBLOCK_ID);
	}

	public static function mergeElementsSections($OLD_IBLOCK_ID, $NEW_IBLOCK_ID, $IBLOCK_CODE = false){
		Capsule::connection('new')->table('iblock_element')->where('IBLOCK_ID', $NEW_IBLOCK_ID)->select('ID', 'IBLOCK_SECTION_ID', 'XML_ID')->orderBy('ID')->chunk(100, function($elements){
			
			foreach ($elements as $element) {
				if(!empty($element->IBLOCK_SECTION_ID) && $element->IBLOCK_SECTION_ID > 0){					
					$sections = IBlockSectionElement::where('IBLOCK_ELEMENT_ID', $element->XML_ID);

					if($sections->exists()){
						$new_sections = [];
						foreach($sections->get() as $section){
							$IBLOCK_SECTION_ID = IBlockSection::getNewID($section->IBLOCK_SECTION_ID);

							if(!empty($IBLOCK_SECTION_ID)){
								$new_sections[] = [
									'IBLOCK_SECTION_ID' => $IBLOCK_SECTION_ID,
									'IBLOCK_ELEMENT_ID' => $element->ID,
								];
							}
						}

						if(!empty($new_sections)){
							IBlockSectionElement::setSections($new_sections);
						}
					}
				}
			}
		});
	}

	public static function mergeElementsPicture($NEW_IBLOCK_ID, $IBLOCK_CODE = false){
		Capsule::connection('new')->table('iblock_element')->where('IBLOCK_ID', $NEW_IBLOCK_ID)->select('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'XML_ID')->orderBy('ID')->chunk(100, function($elements){
			
			foreach ($elements as $element) {
				$new_element = [];

				if(!empty($element->PREVIEW_PICTURE)){
					$new_element['PREVIEW_PICTURE'] = File::getNewIDByXMLID($element->PREVIEW_PICTURE, 'news_picture');
				}

				if(!empty($element->DETAIL_PICTURE)){
					$new_element['DETAIL_PICTURE'] = File::getNewIDByXMLID($element->DETAIL_PICTURE, 'news_picture');
				}

				if(!empty($new_element)){
					$new_element['ID'] = $element->ID;
					IBlockElement::setElementBsau3($new_element);
				}
			}
		});
	}

	public static function editSection($NEW_IBLOCK_ID){
		IBlockSection::updateSectionsBsau3($NEW_IBLOCK_ID);
	}
}

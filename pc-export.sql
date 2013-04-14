select 
	'' as p_id, 
	p.id_product, 
	CONCAT(s.name, ' ', pl.name, ' ', al.name),
	pa.price,
	'' as price_from,
	'' as price_to,
	'руб.' as currency,
	CONCAT(
		'http://www.zoo-cafe.ru/img/p/', 
		MID(i.id_image,1, 1), 
		IF(LENGTH(MID(i.id_image,2, 1)) > 0, CONCAT('/', MID(i.id_image,2, 1)), ''),
		IF(LENGTH(MID(i.id_image,3, 1)) > 0, CONCAT('/', MID(i.id_image,3, 1)), ''),
		IF(LENGTH(MID(i.id_image,4, 1)) > 0, CONCAT('/', MID(i.id_image,4, 1)), ''),
		'/', i.id_image, '.jpg'),
	IF(cl.name='Для собак', 'http://www.pulscen.ru/price/480103-korm-dlja-sobak', 'http://www.pulscen.ru/price/480108-korm-dlja-koshek'),
	pl.description_short,
	s.name, cl.name, al.name, '', '',
	pl.description,
	'наличие', '', '', 'да'
from ps_product p
inner join ps_product_lang pl on pl.id_product=p.id_product
inner join ps_product_attribute pa on pa.id_product=p.id_product
inner join ps_product_attribute_combination pac on pac.id_product_attribute=pa.id_product_attribute
inner join ps_attribute_lang al on al.id_attribute=pac.id_attribute
inner join ps_supplier s on s.id_supplier=p.id_supplier
inner join ps_image i on i.id_product=p.id_product and i.position=1
inner join ps_category_product cp on cp.id_product=p.id_product
inner join ps_category_lang cl on cl.id_category=cp.id_category
inner join ps_category cat on cat.id_category=cp.id_category and cat.level_depth=2
order by p.id_product

import re
from parse_annots import objects, page_ids, annots, get_annots_from_obj

p8_id = page_ids[7]
print('Page 8 id:', p8_id)
p8_content = objects[p8_id]
a_ids = get_annots_from_obj(p8_content)
print('Page 8 annot ids:', a_ids)

for aid in a_ids:
    if aid in annots:
        a = annots[aid]
        print(f"Obj {aid}: Comment = '{a['comment']}', Rect = {a['rect']}")

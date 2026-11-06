import re
import zlib

pdf_path = r'C:/Users/hp/.gemini/antigravity/brain/e08970b2-dd37-4424-a622-b59265e82531/.user_uploaded/media_1793845101297_60a7c3e0.pdf'
with open(pdf_path, 'rb') as f:
    data = f.read()

obj_pattern = re.compile(rb'(\d+)\s+(\d+)\s+obj\b(.*?)endobj', re.DOTALL)
objects = {}
for m in obj_pattern.finditer(data):
    obj_id = int(m.group(1))
    objects[obj_id] = m.group(3)

kids_m = re.search(rb'/Kids\s*\[(.*?)\]', data, re.DOTALL)
page_ids = [int(x) for x in re.findall(rb'(\d+)\s+\d+\s+R', kids_m.group(1))]

def get_annots_from_obj(content):
    ann_m = re.search(rb'/Annots\s*\[(.*?)\]', content, re.DOTALL)
    if ann_m:
        return [int(x) for x in re.findall(rb'(\d+)\s+\d+\s+R', ann_m.group(1))]
    ind_m = re.search(rb'/Annots\s*(\d+)\s+\d+\s+R', content)
    if ind_m:
        target_id = int(ind_m.group(1))
        target_content = objects.get(target_id, b'')
        arr_m = re.search(rb'\[(.*?)\]', target_content, re.DOTALL)
        if arr_m:
            return [int(x) for x in re.findall(rb'(\d+)\s+\d+\s+R', arr_m.group(1))]
    return []

annots = {}
for obj_id, content in objects.items():
    c_m = re.search(rb'/Contents\s*(\((.*?)\)|<([0-9a-fA-F]+)>)', content, re.DOTALL)
    txt = ''
    if c_m:
        if c_m.group(2):
            raw = c_m.group(2)
            txt = raw.decode('latin1', errors='ignore')
            if txt.startswith('\xfe\xff'):
                txt = raw[2:].decode('utf-16-be', errors='ignore')
        elif c_m.group(3):
            b = bytes.fromhex(c_m.group(3).decode('ascii'))
            if b.startswith(b'\xfe\xff'):
                txt = b[2:].decode('utf-16-be', errors='ignore')
            else:
                txt = b.decode('latin1', errors='ignore')
    
    rect_m = re.search(rb'/Rect\s*\[(.*?)\]', content)
    rect = rect_m.group(1).decode('latin1') if rect_m else ''
    subtype_m = re.search(rb'/Subtype\s*/(\w+)', content)
    st = subtype_m.group(1).decode() if subtype_m else ''
    
    annots[obj_id] = {
        'id': obj_id,
        'subtype': st,
        'comment': txt,
        'rect': rect,
        'content': content
    }

for page_num, p_id in enumerate(page_ids, 1):
    p_content = objects.get(p_id, b'')
    a_ids = get_annots_from_obj(p_content)
    page_comments = []
    for a_id in a_ids:
        # Check direct annot
        if a_id in annots and annots[a_id]['comment']:
            page_comments.append((a_id, annots[a_id]['subtype'], annots[a_id]['comment'], annots[a_id]['rect']))
        # Check popup
        a_obj = objects.get(a_id, b'')
        popup_m = re.search(rb'/Popup\s*(\d+)\s+\d+\s+R', a_obj)
        if popup_m:
            pop_id = int(popup_m.group(1))
            if pop_id in annots and annots[pop_id]['comment']:
                page_comments.append((pop_id, annots[pop_id]['subtype'], annots[pop_id]['comment'], annots[pop_id]['rect']))
    
    # deduplicate
    seen = set()
    dedup = []
    for cid, st, ctxt, crect in page_comments:
        if (ctxt, crect) not in seen:
            seen.add((ctxt, crect))
            dedup.append((cid, st, ctxt, crect))
            
    if dedup:
        print(f"\n==================== PAGE {page_num} ====================")
        for cid, st, ctxt, crect in dedup:
            print(f"[{st}] (Rect: {crect}):\n  -> \"{ctxt}\"")

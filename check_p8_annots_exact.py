import zlib
import re

pdf_path = r'C:/Users/hp/.gemini/antigravity/brain/e08970b2-dd37-4424-a622-b59265e82531/.user_uploaded/media_1793845101297_60a7c3e0.pdf'
with open(pdf_path, 'rb') as f:
    data = f.read()

# Let's inspect comments 5, 6, 7, 8 on page 8 and 9
from parse_annots import objects, page_ids, annots, get_annots_from_obj

# Let's inspect all annotations on page 8 and 9
for p_idx in [7, 8]: # page 8 and page 9
    pid = page_ids[p_idx]
    p_content = objects[pid]
    a_ids = get_annots_from_obj(p_content)
    print(f"=== PAGE {p_idx+1} ANNOTS ===")
    for aid in a_ids:
        a_obj = objects.get(aid, b'')
        c_m = re.search(rb'/Contents\s*(\((.*?)\)|<([0-9a-fA-F]+)>)', a_obj, re.DOTALL)
        cmt = ''
        if c_m:
            if c_m.group(2):
                cmt = c_m.group(2).decode('latin1', errors='ignore')
            elif c_m.group(3):
                cmt = bytes.fromhex(c_m.group(3).decode('ascii')).decode('latin1', errors='ignore')
        
        # also get QuadPoints if any
        qp = re.search(rb'/QuadPoints\s*\[(.*?)\]', a_obj)
        rect = re.search(rb'/Rect\s*\[(.*?)\]', a_obj)
        print(f"ID {aid} | Rect: {rect.group(1).decode() if rect else ''} | Comment: {cmt}")

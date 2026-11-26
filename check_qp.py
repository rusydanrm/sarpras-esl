import zlib
import re

pdf_path = r'C:/Users/hp/.gemini/antigravity/brain/e08970b2-dd37-4424-a622-b59265e82531/.user_uploaded/media_1793845101297_60a7c3e0.pdf'
with open(pdf_path, 'rb') as f:
    data = f.read()

from parse_annots import objects

for aid in [2938, 2952, 2980, 3009, 2995]:
    a_obj = objects[aid]
    c_m = re.search(rb'/Contents\s*(\((.*?)\)|<([0-9a-fA-F]+)>)', a_obj, re.DOTALL)
    cmt = c_m.group(2).decode('latin1', errors='ignore') if c_m and c_m.group(2) else ''
    qp = re.search(rb'/QuadPoints\s*\[(.*?)\]', a_obj)
    print(f"ID {aid}: Comment = '{cmt}'")
    if qp:
        print(f"   QuadPoints: {qp.group(1).decode()}")

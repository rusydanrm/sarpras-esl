import zlib
import re

pdf_path = r'C:/Users/hp/.gemini/antigravity/brain/e08970b2-dd37-4424-a622-b59265e82531/.user_uploaded/media_1793845101297_60a7c3e0.pdf'
with open(pdf_path, 'rb') as f:
    data = f.read()

from parse_annots import objects, page_ids
p8_content = objects[page_ids[7]]
# get contents id
c_m = re.search(rb'/Contents\s*(\d+)\s+\d+\s+R', p8_content)
cid = int(c_m.group(1))
raw = objects[cid]
sm = re.search(rb'stream\r?\n(.*?)\r?\nendstream', raw, re.DOTALL)
dec = zlib.decompress(sm.group(1))

# Let's inspect the text operators
lines = dec.decode('latin1', errors='ignore').split('\n')
for line in lines:
    if 'Tj' in line or 'TJ' in line or 'Tm' in line or 'Td' in line:
        if any(w in line for w in ['rekomendasi', 'sarpras', 'inventaris', 'Framework', 'Penelitian', 'Rumusan', 'Tujuan', 'Manfaat', 'Black Box']):
            print(line[:120])

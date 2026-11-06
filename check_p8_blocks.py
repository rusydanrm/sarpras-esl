import zlib
import re

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

# page 8
p8_content = objects[page_ids[7]]
c_m = re.search(rb'/Contents\s*(\d+)\s+\d+\s+R', p8_content)
cid = int(c_m.group(1))
raw = objects[cid]
sm = re.search(rb'stream\r?\n(.*?)\r?\nendstream', raw, re.DOTALL)
dec = zlib.decompress(sm.group(1))

# Let's extract all text with positions if possible, or just plain text
# In PDF, text blocks are between BT and ET
blocks = re.findall(rb'BT\s*(.*?)\s*ET', dec, re.DOTALL)
print(f'Total BT-ET blocks on Page 8: {len(blocks)}')
for b in blocks:
    # find all TJ / Tj
    tjs = re.findall(rb'(\((.*?)\)\s*Tj|\[(.*?)\]\s*TJ)', b)
    txt = ''
    for tj in tjs:
        if tj[1]:
            txt += tj[1].decode('latin1', errors='ignore')
        elif tj[2]:
            parts = re.findall(rb'\((.*?)\)', tj[2])
            txt += ''.join([p.decode('latin1', errors='ignore') for p in parts])
    if txt.strip():
        print('TEXT BLOCK:', txt.strip())

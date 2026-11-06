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

def get_page_text(p_id):
    p_content = objects.get(p_id, b'')
    # get /Contents
    c_m = re.search(rb'/Contents\s*(\d+)\s+\d+\s+R', p_content)
    if not c_m:
        # maybe an array of contents
        c_arr = re.search(rb'/Contents\s*\[(.*?)\]', p_content, re.DOTALL)
        if c_arr:
            c_ids = [int(x) for x in re.findall(rb'(\d+)\s+\d+\s+R', c_arr.group(1))]
        else:
            return ''
    else:
        c_ids = [int(c_m.group(1))]
    
    text_out = []
    for cid in c_ids:
        raw_stream = objects.get(cid, b'')
        sm = re.search(rb'stream\r?\n(.*?)\r?\nendstream', raw_stream, re.DOTALL)
        if sm:
            try:
                dec = zlib.decompress(sm.group(1))
                # extract Tj and TJ
                for tj in re.findall(rb'\((.*?)\)\s*Tj', dec):
                    text_out.append(tj.decode('latin1', errors='ignore'))
                for tj_arr in re.findall(rb'\[(.*?)\]\s*TJ', dec):
                    parts = re.findall(rb'\((.*?)\)', tj_arr)
                    text_out.append(''.join([p.decode('latin1', errors='ignore') for p in parts]))
            except Exception as e:
                pass
    return ' '.join(text_out)

for p_num in [7, 8, 9, 10, 16, 17, 18, 20]:
    pid = page_ids[p_num - 1]
    ptxt = get_page_text(pid)
    print(f"\n==================== TEXT PAGE {p_num} ====================")
    print(ptxt[:1200])

import re

pdf_path = r'C:/Users/hp/.gemini/antigravity/brain/e08970b2-dd37-4424-a622-b59265e82531/.user_uploaded/media_1793845101297_60a7c3e0.pdf'
with open(pdf_path, 'rb') as f:
    data = f.read()

# Let's search for '/Type'
types = re.findall(rb'/Type\s*/(\w+)', data)
from collections import Counter
print('Types in PDF:', Counter(types))

# Look for Kids
kids = re.findall(rb'/Kids\s*\[(.*?)\]', data, re.DOTALL)
print('Kids found:', len(kids))
for k in kids:
    print('Kid refs count:', len(re.findall(rb'(\d+)\s+0\s+R', k)))

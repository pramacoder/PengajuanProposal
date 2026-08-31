import os
import re

dirs_to_search = ['app', 'resources\\views']

def replace_in_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    new_content = content
    
    # PHP Object properties
    new_content = new_content.replace('$proposal->judul_proposal ?? $proposal->judul', '$proposal->judul')
    new_content = new_content.replace('$proposal->judul_proposal', '$proposal->judul')
    
    # PHP Array keys
    new_content = re.sub(r"'judul_proposal'\s*=>", "'judul' =>", new_content)
    new_content = re.sub(r'"judul_proposal"\s*=>', '"judul" =>', new_content)
    
    # Array access
    new_content = new_content.replace("['judul_proposal']", "['judul']")
    new_content = new_content.replace('["judul_proposal"]', '["judul"]')
    
    # Optional object access if any
    new_content = new_content.replace("->judul_proposal", "->judul")
    
    # Make sure we didn't accidentally mess up judul_proposal_lolos_pimnas
    new_content = new_content.replace("->judul_lolos_pimnas", "->judul_proposal_lolos_pimnas")
    new_content = new_content.replace("['judul_lolos_pimnas']", "['judul_proposal_lolos_pimnas']")
    new_content = new_content.replace("'judul_lolos_pimnas'", "'judul_proposal_lolos_pimnas'")
    new_content = new_content.replace('"judul_lolos_pimnas"', '"judul_proposal_lolos_pimnas"')
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

for root, _, files in os.walk('.'):
    # Only search in specific dirs
    if not any(d in root for d in dirs_to_search):
        continue
        
    for file in files:
        if file.endswith('.php'):
            filepath = os.path.join(root, file)
            replace_in_file(filepath)

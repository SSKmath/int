#include <iostream>
#include <vector>
#include <string>
#include <memory>

using namespace std;

struct TrieNode 
{
    TrieNode* children[10];
    int count;

    TrieNode()
    {
        count = 0;
        fill(begin(children), end(children), nullptr);
    }
};

class Trie 
{
public:
    TrieNode* root;

    Trie() 
    {
        root = new TrieNode();
    }

    void insert(string& number) 
    {
        TrieNode* node = root;
        for (char digit : number) 
        {
            int index = digit - '0';
            if (!node->children[index])
                node->children[index] = new TrieNode();
            node = node->children[index];
            node->count++;
        }
    }

    int minAdditionalDigits(string& prefix) 
    {
        TrieNode* node = root;
        for (char digit : prefix) 
        {
            int index = digit - '0';
            if (!node->children[index])
                return -1;
            node = node->children[index];
        }

        if (node->count == 0)
            return -1;

        int additionalDigits = 0;
        while (node->count > 1) 
        {
            additionalDigits++;
            bool foundChild = false;
            for (int i = 0; i < 10; i++) 
            {
                if (node->children[i]) 
                {
                    node = node->children[i];
                    foundChild = true;
                    break;
                }
            }
            if (!foundChild)
                return -1;
        }

        return additionalDigits;
    }
};

int main() 
{
    int N, Q;
    cin >> N >> Q;

    Trie trie;

    for (int i = 0; i < N; ++i) 
    {
        string number;
        cin >> number;
        trie.insert(number);
    }

    for (int i = 0; i < Q; ++i) 
    {
        string prefix;
        cin >> prefix;
        cout << trie.minAdditionalDigits(prefix) << endl;
    }
}
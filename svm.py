import tensorflow as tf
import numpy as np
import csv
import re
import nltk
from nltk.corpus import stopwords
from tensorflow.contrib import learn
import math

def clean_str(string):
	"""
    Tokenization/string cleaning for all datasets except for SST.
    Original taken from https://github.com/yoonkim/CNN_sentence/blob/master/process_data.py
    """
	string = re.sub(r"[^A-Za-z0-9(),!?\'\`]", " ", string)
	string = re.sub(r"\'s", " \'s", string)
	string = re.sub(r"\'ve", " \'ve", string)
	string = re.sub(r"n\'t", " n\'t", string)
	string = re.sub(r"\'re", " \'re", string)
	string = re.sub(r"\'d", " \'d", string)
	string = re.sub(r"\'ll", " \'ll", string)
	string = re.sub(r",", " , ", string)
	string = re.sub(r"!", " ! ", string)
	string = re.sub(r"\(", " \( ", string)
	string = re.sub(r"\)", " \) ", string)
	string = re.sub(r"\?", " \? ", string)
	string = re.sub(r"\s{2,}", " ", string)
	
	return string.strip().lower()


def load_data_and_labels(data_file,numSentence):
	tweets = list()
	labels = list()
	cleaned = list()
	
	i = 0
	with open(data_file) as csvfile:
		d_reader = csv.reader(csvfile, delimiter=',', quotechar='|')
		for row in d_reader:
			if i>=numSentence:
				break	
			tweets.append(row[3])
			if(int(row[1])==0):
				labels.append(-1)
			else:
				labels.append(row[1])
			i+=1
	#print(tweets)

	clean_tweets = [clean_str(sent) for sent in tweets]
	cachedStopWords = stopwords.words("english")

	for t in clean_tweets:
		t = ' '.join([word for word in t.split() if word not in cachedStopWords])
		cleaned.append(t)
	return [cleaned, labels]

# pegasos svm algorithm
def traning(X,Y,iter):
	t = 0
	W = np.zeros((1,X.shape[1])) 
	W = np.concatenate((W,[[1]]),axis=1)

	for i in range(iter):
		for j in range(X.shape[0]): # go through every sentece
			t=t+1;
			lamda = 2;
			yta = 1/(t*lamda)
			x = X[j,:]
			y = float(Y[j])
			x = np.concatenate((x,[0.3]),axis=0)
			x = np.reshape(x,(1,x.shape[0]))
		#	print(W.shape)
		#	print(x.shape)

			product = np.dot(W,x.transpose())
		#	print(y)
		#	print(product[0])
		#	print(float(y)*float(product[0][0]))
		#	print(x.shape)
		#	print(np.multiply(yta*y,x))
			if float(y)*float(product[0])<1.0:
			#	print(np.multiply((1-yta*lamda),W))
			#	print(np.multiply(yta*y,x))
				W = np.multiply((1-yta*lamda),W)+np.multiply(yta*y,x)
			#	print(W)
			else:
				W = np.multiply((1-yta*lamda),W)
			
	#print(W)
	return W

#def prediction(W,X):

	

def accuracy(predictedLabel,trueLabel):
	score = 0
	for i,j in zip(predictedLabel,trueLabel):
		score = score+math.fabs((i-int(j)))
	# the range is between 1 and -1, so divide by 2
	return 1-score/(2*len(predictedLabel))

def prediction(W,X):   # X is a row,W is a row
	#print(W.shape)
	X = X.reshape(-1,1)
	if X.shape[0]!=1:
		X = X.transpose()
	
	while(X.shape[1]<W.shape[1]):
		if(X.shape[1]==W.shape[1]):
			X  = np.concatenate((X,[[0.3]]),axis=1)
		else:
			X  = np.concatenate((X,[[0]]),axis=1)
		#print(X.shape)
	if(X.shape[1]>W.shape[1]):
		X = X[0,0:W.shape[1]-1]
	if np.dot(W,X.transpose())>0:
		return 1
	else:
		return 0

def build_word_label(text):
	dic = {}
	for x in text:
		sentece = x.split(" ")
		for w in sentece:
			dic[w] = len(dic.keys())
	return dic

# use my own method instead of vocab_processor.fit_transform
def build_matrix(maxDocLength,text,numSentence,dic):
	mat = np.zeros((numSentence,maxDocLength))
	rowCounter=0
	#print(text)
	for x in text:
		sentece = x.split(" ")
		colCounter = 0
		for w in sentece:
			mat[rowCounter,colCounter] = dic.get(w,0)
			colCounter+=1
		rowCounter+=1
		#print(rowCounter)
	return mat
	#print(mat)

def save_dic(dic,max_document_length):
	file = open("WordIndex.txt","w") 
	file.write(str(max_document_length))

	for key,val in dic.items():
		file.write("\n")
		file.write(str(key))
		file.write(" ")
		file.write(str(val))
		
	file.close() 

#Loading data
numSentence = 500000
x_text, y = load_data_and_labels('Dataset.csv',numSentence)
dic = build_word_label(x_text)

#Building vocabulary
max_document_length = max([len(x.split(" ")) for x in x_text])
vocab_processor = learn.preprocessing.VocabularyProcessor(max_document_length)
print(vocab_processor)
x = np.array(list(vocab_processor.fit_transform(x_text)))
print(max_document_length)

# use my function for vector construction from a word and save it into text file for use
mat = build_matrix(max_document_length,x_text,numSentence,dic)   
save_dic(dic,max_document_length)


W = traning(mat[:math.floor(mat.shape[0]*0.9)],y[:math.floor(mat.shape[0]*0.9)],10)
x_test = mat[math.floor(mat.shape[0]*.9):]
y_test = y[math.floor(mat.shape[0]*.9):]
predictedLabel = []
trueLabel = []
for i in x_test:
	predictedLabel.append(prediction(W,i))

for i in y_test:
	trueLabel.append(i)

print(accuracy(predictedLabel,trueLabel))  


#!/usr/bin/python
# -*- coding: utf8 -*-

import psyco
psyco.full()

# FUNCTIONS

def DecToBin(number):
	s = ''

	while (number != 0):
		s += str(number % 2)
		number = (number >> 1)

	return s

def IsPalindromicTen(n):
	return (n == n[::-1])
	
def IsBinPalindromic(n):
	binnum = DecToBin(int(n))
	return (binnum == binnum[::-1])

if __name__ == "__main__":
	print "+----------------------------------------------+"
	print "| Find the sum of all numbers, below 1 million |"
	print "| which are palindromic in base 10 and base 2  |"
	print "+----------------------------------------------+"
	
	sum = 0
	for i in xrange(1, 1000000, 2):
		if IsPalindromicTen(str(i)) == True and IsBinPalindromic(str(i)) == True:
			sum += i
	print sum

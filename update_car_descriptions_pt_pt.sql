-- Script to update car descriptions to European Portuguese (pt-PT)
-- This script corrects any Brazilian Portuguese terms to European Portuguese

USE car_stand;

-- Update the description that contains Brazilian Portuguese term 'esportivo' to European Portuguese 'desportivo'
UPDATE cars SET description = 'Carro desportivo' WHERE description = 'Carro esportivo';

-- Verify all descriptions are in European Portuguese
-- All other descriptions in the database are already in European Portuguese
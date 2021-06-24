import re
def devide_line(s):
    #выделение id
    id_end = re.match(r'\d*', s).end()
    id = s[:id_end]
    #выделение year
    year = "NULL"
    title = "NULL"
    genres = "NULL"
    if re.search(r'[(]\d\d\d\d[)]',s) is None:
        if re.search(r'(no genres listed)', s) is None:
            title_end = s.rfind(",")
            title = s[id_end+1: title_end]
            genres = s[title_end+1:]
            genres = genres.replace(",","")
        else:
            genres = "(no genres listed)"        
            null_genr_start = re.search(r'(no genres listed)', s).start()
            title = s[id_end+1:null_genr_start-2]
    else:
        #выделение year
        year_end = re.search(r'[(]\d\d\d\d[)]',s).end()
        year_start = re.search(r'[(]\d\d\d\d[)]',s).start()
        year = s[year_start+1:year_end-1]
        #выделение genres  
        genres = s[year_end+1:]
        genres = genres.replace(",","")
        #выделение title 
        title = s[id_end+1:year_start-1]
    
    title = title.replace("'", "‘")        
    return [id, title, year, genres]


with open("db_init.sql", "w", encoding="utf-8") as db_file:
    #создание или открытие БД
    print(".open movies_rating.db\n", file = db_file)
    #проверка на существование таблиц и их удаление, если они существуют
    print("DROP TABLE IF EXISTS movies;\n", file = db_file)
    print("DROP TABLE IF EXISTS ratings;\n", file = db_file)
    print("DROP TABLE IF EXISTS tags;\n", file = db_file)
    print("DROP TABLE IF EXISTS users;\n", file = db_file)
    print("DROP TABLE IF EXISTS users_occupation;\n", file = db_file)
    print("DROP TABLE IF EXISTS movie_genres;\n", file = db_file)
    print("DROP TABLE IF EXISTS occupations;\n", file = db_file)
    print("DROP TABLE IF EXISTS genres;\n", file = db_file)    
    #создание таблиц
    print("CREATE TABLE movies (id INTEGER NOT NULL PRIMARY KEY, title TEXT, year TEXT);\n", file = db_file)
    print("CREATE TABLE ratings(id INTEGER NOT NULL PRIMARY KEY, user_id INTEGER, movie_id INTEGER, rating REAL, timestamp INTEGER);\n", file = db_file)
    print("CREATE TABLE tags(id INTEGER NOT NULL PRIMARY KEY, user_id INTEGER, movie_id INTEGER, tag TEXT, timestamp INTEGER);\n", file = db_file)
    print("CREATE TABLE users(id INTEGER NOT NULL PRIMARY KEY, name TEXT, surname TEXT, email TEXT, gender TEXT CHECK (gender = 'male' OR gender = 'female'), register_date TEXT NOT NULL);\n", file = db_file)
    print("CREATE TABLE occupations(id INTEGER NOT NULL PRIMARY KEY, occupation TEXT NOT NULL);\n", file = db_file)
    print("CREATE TABLE genres(id INTEGER NOT NULL PRIMARY KEY, genre TEXT NOT NULL);\n", file = db_file)
    print("CREATE TABLE movie_genres(id INTEGER NOT NULL PRIMARY KEY, movie_id INTEGER NOT NULL, genre_id INTEGER NOT NULL);\n", file = db_file) 
    print("CREATE TABLE users_occupation(id  INTEGER NOT NULL PRIMARY KEY, user_id INTEGER NOT NULL, occupation_id INTEGER NOT NULL);\n", file = db_file)    
    #заполнение таблицы occupations
    
    print("INSERT INTO occupations (id, occupation)\n", file = db_file)
    print("VALUES", end=' ', file = db_file)
    occup = [] #создание списка пррофессий для зополнения users_occupation
    with open("occupation.txt","r") as occupation:
        occup = occupation.readlines()
        for i in range(len(occup)):
            occup[i] = occup[i].replace("\n","")
        k=1
        for i in occup:
            i = i.replace("'", "‘")
            print(f"({k},'{i}')", end='',file = db_file)
            if k == len(occup):
                print(";",file = db_file)
            else:
                print(",",file = db_file)
            k+=1  
            
    #Заполнение таблицы genres
    print("INSERT INTO genres (id, genre)\n", file = db_file)
    print("VALUES", end=' ', file = db_file)
    genr = []#создание списка жанров для зополнения movie_genres
    with open("genres.txt","r") as genres:
        genr = genres.readlines()
        for i in range(len(genr)):
            genr[i] = genr[i].replace("\n","")
            genr[i] = genr[i].replace("'","‘")
        k=1
        for i in genr:
            i = i.replace("'", "‘")
            print(f"({k},'{i}')", end='',file = db_file)
            if k == len(genr):
                print(";",file = db_file)
            else:
                print(",",file = db_file)
            k+=1     
    
       
    
            
    #заполнение таблицы users
    print("INSERT INTO users (id, name, surname, email, gender, register_date)\n", file = db_file)
    print("VALUES", end=' ', file = db_file)
    with open("users.txt","r") as users:
        file = users.readlines()
        k=1
        for i in file:
            i = i.replace("'", "‘")
            fields = i[:-1].split("|")
            name = fields[1].split(" ")
            if len(name)==1:
                print(f"({fields[0]},'{fields[1]}',NULL,'{fields[2]}','{fields[3]}','{fields[4]}')", end='',file = db_file)
            else:
                print(f"({fields[0]},'{name[0]}','{name[1]}','{fields[2]}','{fields[3]}','{fields[4]}')", end='',file = db_file)
            if k == len(file):
                print(";",file = db_file)
            else:
                print(",",file = db_file)
            k+=1 
            
    #print(genr)       
    #заполнение таблицы users_occupation
    print("INSERT INTO users_occupation (id, user_id, occupation_id)\n", file = db_file)
    print("VALUES", end=' ', file = db_file)
    with open("users.txt","r") as users:
        file = users.readlines()
        k=1
        for i in file:
            i = i.replace("'", "‘")
            fields = i[:-1].split("|")
            occup_ind = occup.index(fields[5])+1
            print(f"({k},{fields[0]},{occup_ind})", end='',file = db_file)
            if k == len(file):
                print(";",file = db_file)
            else:
                print(",",file = db_file)
            k+=1 


            
    #заполнение таблицы movies
    print("INSERT INTO movies (id, title, year)\n", file = db_file)
    print("VALUES", end=' ', file = db_file)
    with open("movies.csv","r") as movies:
        file = movies.readlines()[1:]
        k=1
        for i in file:
            fields = devide_line(i[:-1])
            print(f"({fields[0]},'{fields[1]}',{fields[2]})", end='',file = db_file)
            if k == len(file):
                print(";",file = db_file)
            else:
                print(",",file = db_file)
            k+=1 

    #заполнение таблицы movie_genres
    print("INSERT INTO movie_genres (id, movie_id, genre_id)\n", file = db_file)
    print("VALUES", end=' ', file = db_file)
    with open("movies.csv","r") as movies:
        file = movies.readlines()[1:]
        k=1
        num = 1
        for i in file:
            fields = devide_line(i[:-1])
            gnrs = fields[3].split("|")
            for j in range(len(gnrs)):
                genr_ind = genr.index(gnrs[j])+1
                print(f"({num},{fields[0]},{genr_ind})", end='',file = db_file)
                if j != len(gnrs)-1:
                    print(",", file = db_file)
                num+=1
            if k == len(file):
                print(";",file = db_file)
            else:
                print(",",file = db_file)
            k+=1  
    
    #заполнение таблицы ratings
    print("INSERT INTO ratings (id, user_id, movie_id, rating, timestamp)\n", file = db_file)
    print("VALUES", end=' ', file = db_file)
    with open("ratings.csv","r") as ratings:
        file = ratings.readlines()[1:]
        k=1
        for i in file:
            fields = i[:-1].split(",")
            print(f"({k},{fields[0]},{fields[1]},{fields[2]},{fields[3]})", end='',file = db_file)
            if k == len(file):
                print(";",file = db_file)
            else:
                print(",",file = db_file)
            k+=1            
            
    #заполнение таблицы tags
    print("INSERT INTO tags (id, user_id, movie_id, tag, timestamp)\n", file = db_file)
    print("VALUES", end=' ', file = db_file)
    with open("tags.csv","r") as tags:
        file = tags.readlines()[1:]
        k=1
        for i in file:
            i = i.replace("'", "‘")
            fields = i[:-1].split(",")
            print(f"({k},{fields[0]},{fields[1]},'{fields[2]}',{fields[3]})", end='',file = db_file)
            if k == len(file):
                print(";",file = db_file)
            else:
                print(",",file = db_file)
            k+=1 